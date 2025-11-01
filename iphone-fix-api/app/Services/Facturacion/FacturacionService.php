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
            // ⚙️ Determinar si la venta fue entregada al cliente
            $entregado = isset($payload['entregado']) ? (bool)$payload['entregado'] : true;

            // 🧾 Crear factura base
            $factura = Factura::create([
                'codigo'         => null,
                'cliente_id'     => $payload['cliente_id'],
                'usuario_id'     => $usuarioId,
                'tipo_venta_id'  => TipoVenta::where('codigo', 'DET')->value('id'),
                'forma_pago_id'  => $payload['forma_pago_id'] ?? null,
                'estado_id'      => EstadoFactura::where('codigo', 'PEND')->value('id'),
                'subtotal'       => 0,
                'impuestos'      => 0,
                'descuentos'     => 0,
                'total'          => 0,
                'fecha_emision'  => now(),
                'prefijo'        => null,
                'consecutivo'    => null,
                'entregado'      => $entregado ? 1 : 0, // 👈 nuevo campo
            ]);

            $subtotal = 0;
            $impuestos = 0;
            $descuentos = 0;

            // 🧮 Detalle de ítems
            foreach ($payload['items'] as $it) {
                $inventario = Inventario::findOrFail($it['inventario_id']);
                $tipoPrecio = strtoupper($it['tipo_precio'] ?? 'DET');

                // 🏷️ Precio individual por ítem
                $valorUnitario = ($tipoPrecio === 'MAY')
                    ? (float)($inventario->precio_mayor ?? $inventario->precio)
                    : (float)$inventario->precio;

                $cantidad = (int)$it['cantidad'];
                $totalItem = $cantidad * $valorUnitario;

                // 🧾 Crear el detalle
                FacturaDetalle::create([
                    'factura_id'     => $factura->id,
                    'tipo_item'      => 'producto',
                    'referencia_id'  => $inventario->id,
                    'tipo_precio'    => $tipoPrecio,
                    'descripcion'    => $it['descripcion'] ?? $inventario->nombre,
                    'cantidad'       => $cantidad,
                    'valor_unitario' => $valorUnitario,
                    'descuento'      => 0,
                    'impuesto'       => 0,
                    'total'          => $totalItem,
                    'entregado'      => $entregado ? 1 : 0, // 👈 importante: sincroniza con la factura
                ]);

                $subtotal += $totalItem;
            }

            // 🧮 Totales generales
            $factura->update([
                'subtotal'  => $subtotal,
                'impuestos' => $impuestos,
                'descuentos'=> $descuentos,
                'total'     => $subtotal + $impuestos - $descuentos,
            ]);

            // 🔢 Recargar y validar código generado por trigger
            $factura->refresh();

            if (empty($factura->codigo)) {
                $codigo = sprintf('FAC-%s-%05d', date('Y'), $factura->id);
                $factura->update([
                    'codigo'      => $codigo,
                    'prefijo'     => 'FAC',
                    'consecutivo' => $factura->id,
                ]);
            }

            // 💰 Pagos (multi medio)
            $totalPagado = 0;
            if (!empty($payload['pagos']) && is_array($payload['pagos'])) {
                foreach ($payload['pagos'] as $pago) {
                    PagoFactura::create([
                        'factura_id'        => $factura->id,
                        'forma_pago_id'     => $pago['forma_pago_id'],
                        'valor'             => $pago['valor'],
                        'referencia_externa'=> $pago['referencia_externa'] ?? null,
                        'observaciones'     => $pago['observaciones'] ?? null,
                        'usuario_id'        => $usuarioId,
                    ]);

                    $totalPagado += $pago['valor'];
                }
            }

            // 💵 Calcular vueltas según monto recibido
            $vueltas = 0;
            if (!empty($payload['monto_recibido'])) {
                $montoRecibido = (float)$payload['monto_recibido'];
                $totalPagado = collect($payload['pagos'] ?? [])->sum('valor');
                $vueltas = max(0, $montoRecibido - $totalPagado);
            }

            DB::commit();

            return [
                'factura' => $factura->load(['detalles', 'cliente', 'usuario', 'estado', 'formaPago', 'pagos']),
                'vueltas' => $vueltas,
            ];

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Crear factura desde una Orden de Servicio cerrada/finalizada
     */
    public function crearFacturaServicio(
        int $ordenId,
        ?int $clienteId = null,
        ?int $formaPagoId,
        int $usuarioId,
        ?string $observaciones = null,
        ?array $equiposSeleccionados = null,
        bool $entregado = true
    ): Factura {
        $os = \App\Models\OrdenServicio\OrdenServicio::with([
            'equipos.tareas',
            'equipos.repuestosInventario',
            'equipos.repuestosExternos'
        ])->findOrFail($ordenId);

        $clienteId = $os->cliente_id;

        $equipos = $os->equipos->filter(function ($eq) {
            return $eq->estado === 'finalizado' && (int)$eq->facturado === 0;
        });

        if (!empty($equiposSeleccionados)) {
            $equipos = $equipos->whereIn('id', $equiposSeleccionados);
        }

        if ($equipos->isEmpty()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'equipos' => 'No hay equipos finalizados pendientes por facturar.'
            ]);
        }

        // LOG: qué equipos encontró y cuántas relaciones carga
        \Log::info('Equipos cargados para prefacturar', [
            'equipos' => $equipos->map(fn ($e) => [
                'id' => $e->id,
                'estado' => $e->estado,
                'facturado' => $e->facturado,
                'tareas_count' => $e->tareas->count(),
                'rep_inv_count' => $e->repuestosInventario->count(),
                'rep_ext_count' => $e->repuestosExternos->count(),
            ])
        ]);

        $tipoSrv   = \App\Models\Parametros\TipoVenta::where('codigo', self::COD_SRV)->firstOrFail();
        $estadoPend= \App\Models\Facturacion\EstadoFactura::where('codigo', self::EST_PEND)->firstOrFail();

        $factura = \App\Models\Facturacion\Factura::create([
            'orden_servicio_id' => $ordenId,
            'cliente_id'        => $clienteId,
            'usuario_id'        => $usuarioId,
            'tipo_venta_id'     => $tipoSrv->id,
            'forma_pago_id'     => $formaPagoId,
            'estado_id'         => $estadoPend->id,
            'subtotal'          => 0,
            'impuestos'         => 0,
            'descuentos'        => 0,
            'total'             => 0,
            'observaciones'     => $observaciones,
            'es_prefactura'     => 0,
            'fecha_emision'     => now(),
            'entregado'         => $entregado ? 1 : 0,
        ]);

        $subtotal = 0;

        foreach ($equipos as $eq) {
            $manoObra = (float)$eq->tareas->sum('costo_aplicado');
            $valorRepInv = (float)$eq->repuestosInventario->sum(fn($r) => $r->cantidad * $r->costo_unitario_aplicado);
            $valorRepExt = (float)$eq->repuestosExternos->sum('costo_total');

            \Log::info('Valores calculados por equipo', [
                'equipo_id' => $eq->id,
                'mano_obra' => $manoObra,
                'repuestos_inventario' => $valorRepInv,
                'repuestos_externos' => $valorRepExt,
            ]);

            $valorTotalEquipo = $manoObra + $valorRepInv + $valorRepExt;
            if ($valorTotalEquipo <= 0) continue;

            \App\Models\Facturacion\FacturaDetalle::create([
                'factura_id'     => $factura->id,
                'tipo_item'      => self::TIPO_ITEM_OS_EQUIPO,
                'referencia_id'  => $eq->id,
                'descripcion'    => "Servicio técnico - Equipo {$eq->imei_serial}",
                'cantidad'       => 1,
                'valor_unitario' => $valorTotalEquipo,
                'total'          => $valorTotalEquipo,
                'entregado'      => $entregado ? 1 : 0,
            ]);

            $subtotal += $valorTotalEquipo;
            $eq->update(['facturado' => 1, 'entregado' => $entregado ? 1 : 0]);
        }

        if ($subtotal <= 0) {
            $factura->delete();
            throw \Illuminate\Validation\ValidationException::withMessages([
                'orden_servicio' => 'No hay valores facturables en los equipos seleccionados.'
            ]);
        }

        $factura->update([
            'subtotal' => $subtotal,
            'total'    => $subtotal,
        ]);

        return $factura->fresh(['cliente', 'detalles', 'estado']);
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

        DB::transaction(function () use ($factura, $estadoAnulada, $usuarioId) {
            // Solo actualiza el estado
            $factura->update([
                'estado_id' => $estadoAnulada->id,
                'updated_at' => now(),
            ]);

            // El trigger se encargará de stock + auditoría
        });

        return $factura->fresh(['estado']);
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
