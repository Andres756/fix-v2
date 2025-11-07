<?php

namespace App\Services\Facturacion;

use App\Models\PlanSepare\PlanSepare;
use App\Models\PlanSepare\AbonoPlanSepare;
use App\Models\PlanSepare\DevolucionPlanSepare;
use App\Models\Inventario\Inventario;
use App\Models\Parametros\FormaPago;
use App\Models\Inventario\MotivoMovimiento;
use App\Models\Facturacion\Factura;
use App\Models\Facturacion\FacturaAuditoria;
use App\Models\Facturacion\PagoFactura;
use App\Services\Facturacion\FacturacionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\Facturacion\FacturaDetalle;

class PlanSepareService
{
    protected $facturacionService;

    public function __construct(FacturacionService $facturacionService)
    {
        $this->facturacionService = $facturacionService;
    }

    /**
     * 🟢 Crear nuevo plan separe
     */
    public function crearPlan(array $data, int $usuarioId): PlanSepare
    {
        return DB::transaction(function () use ($data, $usuarioId) {

            $inventario = Inventario::findOrFail($data['inventario_id']);

            // ⚙️ Validar precio del plan vs inventario
            if ($data['precio_total'] < $inventario->precio) {
                throw ValidationException::withMessages([
                    'precio_total' => 'El valor del plan separe no puede ser inferior al precio comercial del producto (' .
                        number_format($inventario->precio, 0, ',', '.') . ').'
                ]);
            }

            if (!isset($data['porcentaje_minimo'])) {
                throw ValidationException::withMessages([
                    'porcentaje_minimo' => 'Debe especificar el porcentaje mínimo para reservar el producto.'
                ]);
            }

            $porcentaje = (float)$data['porcentaje_minimo'];
            if ($porcentaje < 10 || $porcentaje > 100) {
                throw ValidationException::withMessages([
                    'porcentaje_minimo' => 'El porcentaje de reserva debe estar entre 10% y 100%.'
                ]);
            }

            // 🔒 Verificar disponibilidad del producto
            if ((int)$inventario->reservado === 1 && $inventario->tipo_inventario == 1) {
                throw ValidationException::withMessages([
                    'inventario_id' => 'El producto ya está reservado por otro cliente.'
                ]);
            }

            // ⚠️ Bloquear duplicados para el MISMO cliente sobre el MISMO inventario (solo tipo 1)
            if ((int)$inventario->tipo_inventario_id === 1) {

                // Los estados activos: 1 = ABIERTO, 2 = RESERVADO
                $estadosActivos = [1, 2];

                // Buscar si ya existe un plan activo del mismo cliente sobre ese inventario
                $existeDelMismoCliente = \App\Models\PlanSepare\PlanSepare::query()
                    ->where('cliente_id', $data['cliente_id'])
                    ->where('inventario_id', $inventario->id)
                    ->whereIn('estado_id', $estadosActivos)
                    ->exists();

                if ($existeDelMismoCliente) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'inventario_id' => 'Ya existe un Plan Separe activo de este cliente para este equipo.'
                    ]);
                }
            }

            // 🧾 Crear plan separe
            $plan = PlanSepare::create([
                'cliente_id'       => $data['cliente_id'],
                'inventario_id'    => $inventario->id,
                'precio_total'     => $data['precio_total'],
                'porcentaje_minimo' => $porcentaje,
                'estado_id'        => 1, // ABIERTO
                'usuario_id'       => $usuarioId,
                'observaciones'    => $data['observaciones'] ?? null,
            ]);

            $this->registrarAuditoria($plan->id, $usuarioId, 'CREAR', "Plan separe creado para el producto {$inventario->nombre}.");

            return $plan->load(['cliente', 'inventario', 'estado']);
        });
    }

    /**
     * 💰 Registrar abono
     */
    public function registrarAbono(int $planId, array $data, int $usuarioId)
    {
        return DB::transaction(function () use ($planId, $data, $usuarioId) {

            $plan = PlanSepare::with('inventario')->findOrFail($planId);
            $inventario = $plan->inventario;

            if (in_array($plan->estado?->codigo, ['ANULADO', 'FINALIZADO'])) {
                throw ValidationException::withMessages([
                    'plan' => 'El plan separe ya está cerrado o anulado.'
                ]);
            }

            if ($data['valor'] <= 0) {
                throw ValidationException::withMessages([
                    'valor' => 'El valor del abono debe ser mayor a cero.'
                ]);
            }

            // Calcular total actual
            $totalAbonado = AbonoPlanSepare::where('plan_separe_id', $plan->id)->sum('valor');
            $nuevoTotal = $totalAbonado + $data['valor'];

            // Evitar exceso de pago
            if ($nuevoTotal > $plan->precio_total) {
                throw ValidationException::withMessages([
                    'valor' => 'El abono excede el valor total del plan separe. Saldo restante: $' .
                        number_format($plan->precio_total - $totalAbonado, 0, ',', '.')
                ]);
            }

            // Registrar abono
            AbonoPlanSepare::create([
                'plan_separe_id' => $plan->id,
                'valor'          => $data['valor'],
                'forma_pago_id'  => $data['forma_pago_id'],
                'usuario_id'     => $usuarioId,
            ]);

            // Recalcular total y porcentaje actual
            $nuevoTotal = AbonoPlanSepare::where('plan_separe_id', $plan->id)->sum('valor');
            $porcentajeActual = ($nuevoTotal / $plan->precio_total) * 100;
            $nuevoEstado = $plan->estado_id;

            // Actualizar estado y reserva según progreso
            if ($porcentajeActual >= $plan->porcentaje_minimo && $porcentajeActual < 100) {
                $nuevoEstado = 2; // ASEGURADO / RESERVADO
                if ($inventario && (int)$inventario->tipo_inventario_id === 1) {
                    $inventario->update(['reservado' => 1]);
                }
            } elseif ($porcentajeActual >= 100) {
                $nuevoEstado = 3; // FINALIZADO
                if ($inventario && (int)$inventario->tipo_inventario_id === 1) {
                    $inventario->update(['reservado' => 0]);
                }
                $this->cerrarPlan($plan, $usuarioId);
            }

            // Actualizar plan
            $plan->update([
                'total_abonado' => $nuevoTotal,
                'estado_id'     => $nuevoEstado,
            ]);

            // Registrar auditoría
            $this->registrarAuditoria(
                $plan->id,
                $usuarioId,
                'ABONO',
                'Abono de $' . number_format($data['valor'], 0, ',', '.') .
                ' registrado. Porcentaje actual: ' . round($porcentajeActual, 2) . '%'
            );

            return [
                'plan' => $plan->fresh(['estado', 'inventario:id,nombre,reservado,tipo_inventario_id,precio']),
                'porcentaje_actual' => round($porcentajeActual, 2),
            ];
        });
    }

    public function cerrarPlan(PlanSepare $plan, int $usuarioId): Factura
    {
        if ($plan->factura_id) {
            return Factura::find($plan->factura_id);
        }

        return DB::transaction(function () use ($plan, $usuarioId) {
            $tipoVenta     = \App\Models\Parametros\TipoVenta::where('codigo', 'SEP')->firstOrFail();
            $estadoPagado  = \App\Models\Facturacion\EstadoFactura::where('codigo', 'PAGA')->firstOrFail();

            // 🔒 Bloquea la fila de parámetros para evitar carreras
            $param = \App\Models\Parametros\ParametroFacturacion::lockForUpdate()->firstOrFail();

            // Sube consecutivo y construye el código en la MISMA transacción
            $next = $param->consecutivo_actual + 1;
            $codigo = "{$param->prefijo}-".date('Y')."-".str_pad($next, 5, '0', STR_PAD_LEFT);

            // Reserva el consecutivo
            $param->update(['consecutivo_actual' => $next]);

            // Crea la factura ya con el código único
            $factura = \App\Models\Facturacion\Factura::create([
                'plan_separe_id' => $plan->id,
                'cliente_id'     => $plan->cliente_id,
                'usuario_id'     => $usuarioId,
                'tipo_venta_id'  => $tipoVenta->id,
                'forma_pago_id'  => 1, // efectivo (ajusta si aplica)
                'estado_id'      => $estadoPagado->id,
                'subtotal'       => $plan->precio_total,
                'total'          => $plan->precio_total,
                'observaciones'  => "Liquidación Plan Separe #{$plan->id}",
                'fecha_emision'  => now(),
                'entregado'      => 1,
                'codigo'         => $codigo,
                'prefijo'        => $param->prefijo,
                'consecutivo'    => $next,
            ]);

            \App\Models\Facturacion\FacturaDetalle::create([
                'factura_id'     => $factura->id,
                'tipo_item'      => 'plan_separe',
                'referencia_id'  => $plan->inventario_id,
                'descripcion'    => "Entrega de producto Plan Separe #{$plan->id}",
                'cantidad'       => 1,
                'valor_unitario' => $plan->precio_total,
                'total'          => $plan->precio_total,
                'entregado'      => 1,
            ]);

            // Actualiza estado del plan y linkea factura
            $plan->update([
                'estado_id'  => \DB::table('estados_plan_separe')->where('codigo', 'CER')->value('id'),
                'factura_id' => $factura->id,
            ]);

        // 🧾 Kardex: SALIDA por plan separe (solo si es inventario tipo 1)
        $inv = $plan->inventario;

        if ($inv && (int)$inv->tipo_inventario_id === 1) {

            // 🚨 Validar stock disponible antes de descontar
            if ($inv->stock < 1) {
                throw ValidationException::withMessages([
                    'inventario' => "No hay stock suficiente para entregar el producto {$inv->nombre} ({$inv->codigo}). Stock actual: {$inv->stock}."
                ]);
            }

            // 🔢 Guardar stock anterior y restar 1
            $stockAnterior = $inv->stock;
            $inv->decrement('stock', 1);

            // ✅ Registrar movimiento de salida
            \App\Models\Inventario\MovimientoInventario::create([
                'inventario_id'        => $inv->id,
                'tipo_movimiento'      => 'salida',
                'cantidad'             => 1,
                'stock_anterior'       => $stockAnterior,
                'stock_nuevo'          => $inv->stock,
                'costo_unitario'       => $inv->costo,
                'motivo_id'            => DB::table('motivos_movimientos')->where('codigo', 'salida_plan_separe')->value('id'),
                'documento_referencia' => $factura->codigo,
                'usuario_id'           => $usuarioId,
                'observaciones'        => "Entrega Plan Separe #{$plan->id} - Factura {$factura->codigo}",
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);

            // 🧩 Desactivar el producto y liberar reserva
            $inv->update([
                'reservado' => 0,
                'activo'    => 0, // 🔴 Desactiva automáticamente el producto al venderse
            ]);

            // 🧾 Auditoría opcional
            $this->registrarAuditoria(
                $plan->id,
                $usuarioId,
                'CERRAR',
                "Producto {$inv->nombre} ({$inv->codigo}) entregado y marcado como inactivo tras la venta."
            );
        }

            $this->registrarAuditoria($plan->id, $usuarioId, 'CERRAR', "Plan separe cerrado y facturado como {$factura->codigo}.");

            return $factura->fresh(['detalles','estado']);
        });
    }

    /**
     * 🔁 Reasignar un plan separe a otro inventario
     * Si el cliente ya ha abonado lo suficiente, pasa automáticamente a ASEGURADO.
     */
    public function reasignarPlan(int $planId, int $nuevoInventarioId, int $usuarioId)
    {
        return DB::transaction(function () use ($planId, $nuevoInventarioId, $usuarioId) {
            $plan = PlanSepare::with(['inventario', 'estado'])->findOrFail($planId);
            $inventarioAnterior = $plan->inventario;
            $nuevoInventario = \App\Models\Inventario\Inventario::findOrFail($nuevoInventarioId);

            // 🚫 No permitir reasignar si ya fue facturado
            if (!empty($plan->factura_id)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'plan' => 'Este plan separe ya fue facturado y no puede ser reasignado.'
                ]);
            }

            // 🔎 Validar estado (solo REA o PEN_REA)
            if (!in_array($plan->estado?->codigo, ['REA', 'PEN_REA'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'plan' => 'Solo los planes en estado de REASIGNACIÓN pueden ser reasignados.'
                ]);
            }

            // 🔎 Validar disponibilidad del nuevo inventario
            if ((int)$nuevoInventario->reservado === 1) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'nuevo_inventario_id' => 'El nuevo inventario seleccionado ya está reservado.'
                ]);
            }

            // 🔎 Validar precios (no menor al costo ni al precio original)
            if ($nuevoInventario->precio < $nuevoInventario->costo) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'nuevo_inventario_id' => 'El nuevo inventario no puede tener un precio menor a su costo base.'
                ]);
            }

            if ($nuevoInventario->precio < $plan->precio_total) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'nuevo_inventario_id' => 'El nuevo inventario no puede tener un valor menor al precio del plan original.'
                ]);
            }

            // 🔓 Liberar inventario anterior si estaba reservado
            if ($inventarioAnterior && (int)$inventarioAnterior->reservado === 1) {
                $inventarioAnterior->update(['reservado' => 0]);
            }

            // 🔒 Reservar nuevo inventario si aplica
            if ($nuevoInventario->tipo_inventario_id == 1) {
                $nuevoInventario->update(['reservado' => 1]);
            }

            // 📊 Calcular porcentaje abonado
            $totalAbonado = \App\Models\PlanSepare\AbonoPlanSepare::where('plan_separe_id', $plan->id)->sum('valor');
            $porcentajeAbonado = $plan->precio_total > 0
                ? round(($totalAbonado / $plan->precio_total) * 100, 2)
                : 0;

            // 📈 Determinar estado final según porcentaje abonado
            $estadoNuevo = ($porcentajeAbonado >= $plan->porcentaje_minimo)
                ? \App\Models\PlanSepare\EstadoPlanSepare::where('codigo', 'ASE')->value('id')  // ASEGURADO
                : \App\Models\PlanSepare\EstadoPlanSepare::where('codigo', 'ACT')->value('id'); // ABIERTO

            // 🧾 Actualizar plan separe
            $plan->update([
                'inventario_id' => $nuevoInventario->id,
                'precio_total'  => $nuevoInventario->precio,
                'estado_id'     => $estadoNuevo,
                'observaciones' => "Reasignado al inventario {$nuevoInventario->nombre} ({$nuevoInventario->codigo}).",
                'updated_at'    => now(),
            ]);

            // 🧾 Auditoría detallada
            $this->registrarAuditoria(
                $plan->id,
                $usuarioId,
                'REASIGNAR',
                "Plan separe reasignado del inventario {$inventarioAnterior->nombre} ({$inventarioAnterior->codigo}) "
                . "al nuevo inventario {$nuevoInventario->nombre} ({$nuevoInventario->codigo}). "
                . "Nuevo precio: $" . number_format($nuevoInventario->precio, 0, ',', '.') .
                " | Nuevo % mínimo: {$plan->porcentaje_minimo}%. "
                . "Porcentaje abonado actual: {$porcentajeAbonado}%."
            );

            return $plan->fresh(['inventario', 'estado']);
        });
    }

    /**
     * ❌ Anular plan + devolución configurable (con registro en Kardex)
     */
    public function anularPlan(int $planId, array $data, int $usuarioId)
    {
        return DB::transaction(function () use ($planId, $data, $usuarioId) {

            $plan = PlanSepare::with(['inventario', 'estado'])->findOrFail($planId);

            if (in_array($plan->estado?->codigo, ['CAN', 'DEV'])) {
                throw ValidationException::withMessages([
                    'plan' => 'El plan ya está anulado o devuelto.'
                ]);
            }

            $totalAbonado = (float) \App\Models\PlanSepare\AbonoPlanSepare::where('plan_separe_id', $plan->id)->sum('valor');
            $porcentaje = (float) ($data['porcentaje_devolucion'] ?? 100);
            $montoDevuelto = $totalAbonado > 0 ? $totalAbonado * ($porcentaje / 100) : 0;

            // 🧩 Guardar motivo de anulación
            if (!empty($data['motivo_anulacion_id'])) {
                $plan->update([
                    'motivo_anulacion_id' => $data['motivo_anulacion_id'],
                ]);
            }

            // 💸 Registrar devolución solo si hay abonos y porcentaje > 0
            if ($totalAbonado > 0 && $porcentaje > 0) {
                \App\Models\PlanSepare\DevolucionPlanSepare::create([
                    'plan_separe_id'        => $plan->id,
                    'monto_total'           => $totalAbonado,
                    'monto_devuelto'        => $montoDevuelto,
                    'porcentaje_devolucion' => $porcentaje,
                    'forma_pago_id'         => $data['forma_pago_id'], // requerido solo en este caso
                    'usuario_id'            => $usuarioId,
                    'observaciones'         => $data['observaciones'] ?? 'Devolución al anular plan separe',
                ]);
            }

            // 🔁 resto del flujo igual...
            // (factura, kardex, actualización de estado, auditoría, retorno)

            // Actualizar estado final según haya devolución o no
            $estadoFinal = $montoDevuelto > 0 ? 6 : 5;

            $plan->update(['estado_id' => $estadoFinal]);

            $this->registrarAuditoria(
                $plan->id,
                $usuarioId,
                'ANULAR',
                'Motivo: ' . ($data['motivo'] ?? 'No especificado')
            );

            $plan->refresh();

            return [
                'message' => $estadoFinal === 6
                    ? 'Plan separe devuelto correctamente (con devolución).'
                    : 'Plan separe cancelado correctamente (sin devolución).',
                'plan' => $plan->load(['estado', 'inventario', 'motivoAnulacion']),
                'total_abonado' => $totalAbonado,
                'monto_devuelto' => $montoDevuelto
            ];
        });
    }

    /**
     * 🧾 Registrar auditoría genérica
     */
    private function registrarAuditoria(int $planId, int $usuarioId, string $accion, string $detalle)
    {
        DB::table('plan_separe_auditoria')->insert([
            'plan_separe_id' => $planId,
            'usuario_id' => $usuarioId,
            'accion' => $accion,
            'detalle' => $detalle,
            'created_at' => now(),
        ]);
    }

    /**
     * 📋 Listar planes separe con totales y relaciones principales
     */
    public function listar(int $perPage = 15)
    {
        $planes = PlanSepare::with(['cliente', 'inventario', 'estado'])
            ->withSum('abonos', 'valor') // Calcula la suma total de abonos por plan
            ->orderByDesc('created_at')
            ->paginate($perPage);

        // Renombrar el campo 'abonos_sum_valor' a 'total_abonos'
        $planes->getCollection()->transform(function ($plan) {
            $plan->total_abonos = (float) ($plan->abonos_sum_valor ?? 0);
            unset($plan->abonos_sum_valor);
            return $plan;
        });

        return $planes;
    }

}
