<?php

namespace App\Services\Facturacion;

use App\Models\Facturacion\{
    Factura,
    FacturaDetalle,
    PagoFactura,
    EstadoFactura,
    FacturaAuditoria
};
use App\Models\Parametros\{
    FormaPago,
    TipoVenta,
    ParametroFacturacion
};
use App\Models\Inventario\Inventario;
use App\Models\OrdenServicio\OrdenServicio;
use App\Models\PlanSepare\PlanSepare;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FacturacionService
{
    const TIPO_ITEM_PRODUCTO = 'producto';
    const TIPO_ITEM_OS_EQUIPO = 'orden_servicio_equipo';
    const TIPO_ITEM_PLAN_SEP = 'plan_separe';

    const COD_DET = 'DET';
    const COD_MAY = 'MAY';
    const COD_SRV = 'SRV';
    const COD_SEP = 'SEP';
    const EST_PEND = 'PEND';
    const EST_PAGA = 'PAGA';
    const EST_ANUL = 'ANUL';

    /**
     * Crear factura de venta directa o mayorista
     */
    public function crearFacturaVenta(array $payload, int $usuarioId)
    {
        DB::beginTransaction();

        try {
            // 🧾 Crear factura base (sin totales aún)
            $factura = Factura::create([
                'codigo'         => null,
                'cliente_id'     => $payload['cliente_id'],
                'usuario_id'     => $usuarioId,
                'tipo_venta_id'  => TipoVenta::where('codigo', 'DET')->value('id'), // valor genérico
                'forma_pago_id'  => $payload['forma_pago_id'] ?? null,
                'estado_id'      => EstadoFactura::where('codigo', 'PEND')->value('id'),
                'subtotal'       => 0,
                'impuestos'      => 0,
                'descuentos'     => 0,
                'total'          => 0,
                'fecha_emision'  => now(),
                'prefijo'        => null,
                'consecutivo'    => null,
            ]);

            $subtotal = 0;
            $impuestos = 0;
            $descuentos = 0;

            // 🧮 Recorrer ítems enviados desde el frontend
            foreach ($payload['items'] as $it) {
                $inventario = Inventario::findOrFail($it['inventario_id']);
                $tipoPrecio = strtoupper($it['tipo_precio'] ?? 'DET');

                // 🏷️ Seleccionar precio según tipo de venta por ítem
                if ($tipoPrecio === 'MAY') {
                    $valorUnitario = (float)($inventario->precio_mayor ?? $inventario->precio);
                } else {
                    $valorUnitario = (float)$inventario->precio;
                }

                $cantidad = (int)$it['cantidad'];
                $totalItem = $cantidad * $valorUnitario;

                // 💾 Insertar detalle
                FacturaDetalle::create([
                    'factura_id'     => $factura->id,
                    'tipo_item'      => 'producto',
                    'referencia_id'  => $inventario->id,
                    'tipo_precio'    => $tipoPrecio, // 👈 NUEVO campo
                    'descripcion'    => $it['descripcion'] ?? $inventario->nombre,
                    'cantidad'       => $cantidad,
                    'valor_unitario' => $valorUnitario,
                    'descuento'      => 0,
                    'impuesto'       => 0,
                    'total'          => $totalItem,
                ]);

                $subtotal += $totalItem;
            }

            // 🧮 Actualizar totales en factura
            $factura->update([
                'subtotal'  => $subtotal,
                'impuestos' => $impuestos,
                'descuentos'=> $descuentos,
                'total'     => $subtotal + $impuestos - $descuentos,
            ]);

            // 🔢 Consecutivo y código de factura
            $param = ParametroFacturacion::first();
            $nuevoConsecutivo = $param->consecutivo_actual + 1;
            $codigo = "{$param->prefijo}-" . date('Y') . "-" . str_pad($nuevoConsecutivo, 5, '0', STR_PAD_LEFT);

            $factura->update([
                'codigo'      => $codigo,
                'prefijo'     => $param->prefijo,
                'consecutivo' => $nuevoConsecutivo,
            ]);

            $param->update(['consecutivo_actual' => $nuevoConsecutivo]);

            // 🧠 Registrar auditoría
            FacturaAuditoria::create([
                'factura_id' => $factura->id,
                'usuario_id' => $usuarioId,
                'accion'     => 'CREAR',
                'detalle'    => 'Factura creada manualmente con ítems mixtos (detalle/mayorista)',
            ]);

            DB::commit();

            return $factura->load(['detalles', 'cliente', 'usuario', 'estado', 'formaPago']);

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Crear factura desde una Orden de Servicio cerrada/finalizada
     */
    public function crearFacturaServicio(int $ordenId, int $clienteId, ?int $formaPagoId, int $usuarioId, ?string $observaciones = null): Factura
    {
        $os = OrdenServicio::with(['equipos.tareas'])->findOrFail($ordenId);
        if (!in_array($os->estado, ['finalizada', 'cerrada'])) {
            throw ValidationException::withMessages(['orden_servicio' => 'La OS debe estar finalizada o cerrada para facturar.']);
        }

        $equipos = $os->equipos;
        if ($equipos->isEmpty()) {
            throw ValidationException::withMessages(['orden_servicio' => 'No hay equipos asociados a la OS.']);
        }

        $items = [];
        foreach ($equipos as $eq) {
            $manoObra = (float)$eq->tareas->sum('costo_aplicado');
            $repuestos = (float) DB::table('repuestos_os_inventario')
                ->where('equipo_os_id', $eq->id)
                ->sum(DB::raw('cantidad * costo_unitario_aplicado'));

            $valor = $manoObra + $repuestos;
            if ($valor <= 0) continue;

            $items[] = [
                'equipo_os_id' => $eq->id,
                'descripcion'  => "Servicio técnico - Equipo {$eq->imei_serial}",
                'cantidad'     => 1,
                'valor'        => $valor,
            ];
        }

        if (empty($items)) {
            throw ValidationException::withMessages(['orden_servicio' => 'No hay valores facturables (mano de obra o repuestos).']);
        }

        return DB::transaction(function () use ($clienteId, $usuarioId, $formaPagoId, $observaciones, $items) {
            $tipoSrv = TipoVenta::where('codigo', self::COD_SRV)->firstOrFail();
            $estadoPend = EstadoFactura::where('codigo', self::EST_PEND)->firstOrFail();

            $factura = Factura::create([
                'cliente_id'   => $clienteId,
                'usuario_id'   => $usuarioId,
                'tipo_venta_id'=> $tipoSrv->id,
                'forma_pago_id'=> $formaPagoId,
                'estado_id'    => $estadoPend->id,
                'subtotal'     => 0,
                'total'        => 0,
                'observaciones'=> $observaciones,
            ]);

            $subtotal = 0;
            foreach ($items as $it) {
                FacturaDetalle::create([
                    'factura_id'     => $factura->id,
                    'tipo_item'      => self::TIPO_ITEM_OS_EQUIPO,
                    'referencia_id'  => $it['equipo_os_id'],
                    'descripcion'    => $it['descripcion'],
                    'cantidad'       => 1,
                    'valor_unitario' => $it['valor'],
                    'descuento'      => 0,
                    'impuesto'       => 0,
                    'total'          => $it['valor'],
                ]);
                $subtotal += $it['valor'];
            }

            $factura->update([
                'subtotal' => $subtotal,
                'total'    => $subtotal,
            ]);

            return $factura->fresh(['cliente', 'detalles']);
        });
    }

    /**
     * Registrar pago o abono a factura (actualiza estado por trigger)
     */
    public function registrarPagoFactura(int $facturaId, array $data, int $usuarioId): PagoFactura
    {
        $factura = Factura::findOrFail($facturaId);
        $estado = $factura->estado?->codigo;

        if ($estado === self::EST_ANUL) {
            throw ValidationException::withMessages(['factura' => 'No se pueden registrar pagos sobre una factura anulada.']);
        }

        $pago = new PagoFactura([
            'factura_id'        => $factura->id,
            'forma_pago_id'     => $data['forma_pago_id'] ?? null,
            'valor'             => $data['valor'],
            'referencia_externa'=> $data['referencia_externa'] ?? null,
            'observaciones'     => $data['observaciones'] ?? null,
            'usuario_id'        => $usuarioId,
            'created_at'        => now(),
        ]);

        $pago->save(); // El trigger actualiza estado y auditoría automáticamente
        return $pago;
    }

    /**
     * Anular factura (triggers devuelven stock y registran auditoría)
     */
    public function anularFactura(int $facturaId, int $usuarioId): Factura
    {
        $factura = Factura::findOrFail($facturaId);
        $estado = $factura->estado?->codigo;

        if ($estado === self::EST_ANUL) {
            return $factura;
        }

        $estadoAnulada = EstadoFactura::where('codigo', self::EST_ANUL)->firstOrFail();

        DB::transaction(function () use ($factura, $estadoAnulada) {
            $factura->update(['estado_id' => $estadoAnulada->id]);
            // Trigger se encarga del resto: stock, movimientos, auditoría
        });

        return $factura->fresh();
    }

    /**
     * Listado y reportes desde la vista vw_facturas_resumen
     */
    public function listarResumen(array $filters = [])
    {
        // 🧩 Consulta directa con Eloquent (ya no con DB::table)
        $query = \App\Models\Facturacion\Factura::query()
            ->with(['cliente', 'usuario', 'formaPago', 'estado', 'tipoVenta'])
            ->orderByDesc('fecha_emision');

        // 🔍 Filtros dinámicos
        if (!empty($filters['cliente_id'])) {
            $query->where('cliente_id', $filters['cliente_id']);
        }

        if (!empty($filters['usuario_id'])) {
            $query->where('usuario_id', $filters['usuario_id']);
        }

        if (!empty($filters['estado'])) {
            // puedes filtrar por nombre, código o ID según lo que devuelvas del frontend
            $query->whereHas('estado', function ($q) use ($filters) {
                $q->where('codigo', $filters['estado'])
                ->orWhere('nombre', $filters['estado']);
            });
        }

        if (!empty($filters['tipo_venta_id'])) {
            $query->where('tipo_venta_id', $filters['tipo_venta_id']);
        }

        if (!empty($filters['desde'])) {
            $query->whereDate('fecha_emision', '>=', $filters['desde']);
        }

        if (!empty($filters['hasta'])) {
            $query->whereDate('fecha_emision', '<=', $filters['hasta']);
        }

        // 📊 Paginación con Eloquent (mantiene los casts y relaciones)
        return $query->paginate($filters['per_page'] ?? 15);
    }
}
