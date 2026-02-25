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
use App\Services\Facturacion\AnulacionFacturaService;

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
     * Ahora soporta ventas a clientes Y proveedores
     */
    public function crearFacturaVenta(array $payload, int $usuarioId)
    {
        DB::beginTransaction();

        try {
            // Validar que venga cliente_id O proveedor_id
            $destinatarioTipo = $payload['destinatario_tipo'] ?? 'cliente';
            
            if ($destinatarioTipo === 'cliente' && empty($payload['cliente_id'])) {
                throw ValidationException::withMessages([
                    'cliente_id' => 'Debe proporcionar un cliente para la venta'
                ]);
            }
            
            if ($destinatarioTipo === 'proveedor' && empty($payload['proveedor_id'])) {
                throw ValidationException::withMessages([
                    'proveedor_id' => 'Debe proporcionar un proveedor para la venta'
                ]);
            }

            // ⚙️ Determinar estado inicial
            $estadoPend = EstadoFactura::where('codigo', 'PEND')->firstOrFail();

            // 🧾 Crear factura base
            $factura = Factura::create([
                'codigo'         => null,
                'cliente_id'     => $destinatarioTipo === 'cliente' ? $payload['cliente_id'] : null,
                'proveedor_id'   => $destinatarioTipo === 'proveedor' ? $payload['proveedor_id'] : null,
                'usuario_id'     => $usuarioId,
                'tipo_venta_id'  => TipoVenta::where('codigo', 'DET')->value('id'),
                'forma_pago_id'  => $payload['forma_pago_id'] ?? null,
                'estado_id'      => $estadoPend->id,
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

            // 🧮 Detalle de ítems
            foreach ($payload['items'] as $it) {
                $inventario = Inventario::findOrFail($it['inventario_id']);
                $tipoPrecio = strtoupper($it['tipo_precio'] ?? 'DET');

                // 🏷️ Prioridad: usar el precio enviado desde el frontend si viene
                $valorUnitario = isset($it['precio_unitario']) && is_numeric($it['precio_unitario'])
                    ? (float) $it['precio_unitario']
                    : (($tipoPrecio === 'MAY')
                        ? (float) ($inventario->precio_mayor ?? $inventario->precio_venta ?? 0)
                        : (float) ($inventario->precio_venta ?? 0));

                $cantidad = (int) $it['cantidad'];
                $desc = (float) ($it['descuento'] ?? 0);
                $totalLinea = ($valorUnitario * $cantidad) - $desc;

                // Crear detalle
                FacturaDetalle::create([
                    'factura_id'     => $factura->id,
                    'inventario_id'  => $inventario->id,
                    'descripcion'    => $inventario->nombre,
                    'cantidad'       => $cantidad,
                    'precio_unitario'=> $valorUnitario,
                    'descuento'      => $desc,
                    'impuesto'       => 0,
                    'total'          => $totalLinea,
                    'tipo'           => self::TIPO_ITEM_PRODUCTO,
                    'referencia_id'  => $inventario->id,  // ✅ AGREGAR ESTO
                    'entregado'      => $it['entregado'] ?? false,
                ]);

                // Descontar inventario
                if ($inventario->stock < $cantidad) {
                    DB::rollBack();
                    throw ValidationException::withMessages([
                        'stock' => "No hay suficiente stock para {$inventario->nombre}. Disponible: {$inventario->stock}"
                    ]);
                }

                $inventario->stock -= $cantidad;
                $inventario->save();

                $subtotal += $totalLinea;
            }

            // Actualizar totales en factura
            $factura->subtotal = $subtotal;
            $factura->impuestos = $impuestos;
            $factura->descuentos = $descuentos;
            $factura->total = $subtotal + $impuestos - $descuentos;
            $factura->save();

            // Generar código
            $factura->codigo = 'FAC-' . str_pad($factura->id, 6, '0', STR_PAD_LEFT);
            $factura->save();

            // Auditoría
            FacturaAuditoria::create([
                'factura_id' => $factura->id,
                'usuario_id' => $usuarioId,
                'accion'     => 'EDITAR',  // ✅ Usar un valor que exista en el ENUM
                'detalle'    => $destinatarioTipo === 'cliente' 
                    ? "Factura creada para cliente ID {$payload['cliente_id']}"
                    : "Factura creada para proveedor ID {$payload['proveedor_id']}",
                'created_at' => now(),
            ]);

            DB::commit();

            // Recargar con relaciones
            return $factura->fresh([
                'cliente',
                'proveedor',
                'detalles.inventario',
                'estado',
                'tipoVenta'
            ]);

        } catch (\Exception $e) {
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
        $os = OrdenServicio::with([
            'equipos.tareas',
            'equipos.repuestosInventario.inventario',
            'equipos.repuestosExternos'
        ])->findOrFail($ordenId);

        $clienteId = $os->cliente_id;

        // Equipos pendientes por facturar
        $equipos = $os->equipos->filter(function ($eq) {
            return strtolower(trim($eq->estado)) === 'finalizado' && (int)$eq->facturado === 0;
        });

        if (!empty($equiposSeleccionados)) {
            $equipos = $equipos->filter(fn($eq) => in_array($eq->id, $equiposSeleccionados));
        }

        if ($equipos->isEmpty()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'equipos' => 'No hay equipos finalizados pendientes por facturar.'
            ]);
        }

        $tipoSrv   = \App\Models\Parametros\TipoVenta::where('codigo', self::COD_SRV)->firstOrFail();
        $estadoPend= \App\Models\Facturacion\EstadoFactura::where('codigo', self::EST_PEND)->firstOrFail();

        // 🔎 Buscar si ya existe una factura pendiente para esta orden
        $factura = \App\Models\Facturacion\Factura::where('orden_servicio_id', $ordenId)
            ->where('estado_id', $estadoPend->id)
            ->first();
        
        $estadoPend = \App\Models\Facturacion\EstadoFactura::where('codigo', 'PEND')->firstOrFail();

        $subtotal = 0;

        if (!$factura) {
            // 🆕 Crear nueva factura si no existe
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
                'estado_id' => $estadoPend->id,
            ]);

            // 🔢 Generar código y consecutivo SOLO en la creación
            $param = \App\Models\Parametros\ParametroFacturacion::first();
            if ($param) {
                $nuevoConsec = $param->consecutivo_actual + 1;
                $codigo = "{$param->prefijo}-" . date('Y') . "-" . str_pad($nuevoConsec, 5, '0', STR_PAD_LEFT);
                $factura->update([
                    'codigo'      => $codigo,
                    'prefijo'     => $param->prefijo,
                    'consecutivo' => $nuevoConsec,
                ]);
                $param->update(['consecutivo_actual' => $nuevoConsec]);
            }
        }

        // 🧾 Agregar o sumar detalles de equipos nuevos
        foreach ($equipos as $eq) {
            $manoObra = (float)$eq->tareas->sum('costo_aplicado');
            $valorRepInv = (float)$eq->repuestosInventario->sum(fn($r) => $r->cantidad * $r->costo_unitario_aplicado);
            $valorRepExt = (float)$eq->repuestosExternos->sum('costo_total');
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

            // Actualizar equipo
            $eq->update([
                'facturado' => 1,
                'entregado' => $entregado ? 1 : 0,
            ]);
        }

        // 🧮 Recalcular totales acumulados
        $factura->update([
            'subtotal' => $factura->subtotal + $subtotal,
            'total'    => $factura->total + $subtotal,
        ]);

        // Auditoría
        FacturaAuditoria::create([
            'factura_id' => $factura->id,
            'usuario_id' => $usuarioId,
            'accion'     => 'EDITAR',
            'detalle'    => 'Se agregaron equipos a la factura existente (orden de servicio).',
            'created_at' => now(),
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

    $pago->save();

    // 🔹 1. Verificar si el total pagado cubre toda la factura
    $totalPagado = $factura->pagos()->sum('valor');
    if ($totalPagado >= $factura->total) {
        $estadoPagada = \App\Models\Facturacion\EstadoFactura::where('codigo', self::EST_PAGA)->first();

        if ($estadoPagada) {
            // Actualizar factura y detalles
            $factura->update(['estado_id' => $estadoPagada->id]);
            \App\Models\Facturacion\FacturaDetalle::where('factura_id', $factura->id)
                ->update(['estado_id' => $estadoPagada->id]);

            // Registrar auditoría
            \App\Models\Facturacion\FacturaAuditoria::create([
                'factura_id' => $factura->id,
                'usuario_id' => $usuarioId,
                'accion'     => 'PAGAR',
                'detalle'    => 'Factura completamente pagada. Todos los ítems marcados como pagados.',
                'created_at' => now(),
            ]);
        }
    }

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

            // 🔹 1. Actualizar estado general de la factura
            $factura->update([
                'estado_id' => $estadoAnulada->id,
                'updated_at' => now(),
                'entregado' => 0
            ]);

            // 🔹 2. Actualizar todos los detalles a ANULADO (3)
            \App\Models\Facturacion\FacturaDetalle::where('factura_id', $factura->id)
                ->update(['estado_id' => $estadoAnulada->id, 'entregado' => 0]);

            // 🔹 3. Si la factura proviene de una orden de servicio, revertir los equipos
            if ($factura->orden_servicio_id) {
                $equiposIds = \App\Models\Facturacion\FacturaDetalle::where('factura_id', $factura->id)
                    ->whereNotNull('referencia_id')
                    ->pluck('referencia_id')
                    ->unique()
                    ->toArray();

                if (!empty($equiposIds)) {
                    \App\Models\OrdenServicio\EquipoOrdenServicio::whereIn('id', $equiposIds)
                        ->update(['entregado' => 0, 'facturado' => 0]);
                }
            }

            // 🔹 4. Registrar auditoría
            \App\Models\Facturacion\FacturaAuditoria::create([
                'factura_id' => $factura->id,
                'usuario_id' => $usuarioId,
                'accion'     => 'ANULAR',
                'detalle'    => 'Factura anulada completamente. Se revirtió el estado de todos los ítems.',
                'created_at' => now(),
            ]);
        });

        return $factura->fresh(['estado']);
    }

    public function anularAvanzado(Request $request, $id, AnulacionFacturaService $anulacionService)
    {
        try {
            $resultado = $anulacionService->anularFacturaAvanzado($request->all(), $id);
            return response()->json($resultado, 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al procesar la anulación avanzada',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 🔁 Anular factura generada desde Plan Separe
     */
    public function anularFacturaDesdePlanSepare(int $facturaId, int $usuarioId)
    {
        DB::beginTransaction();
        try {
            $factura = \App\Models\Facturacion\Factura::with(['detalles', 'estado'])->findOrFail($facturaId);

            // 🟡 Obtener estado "ANULADA"
            $estadoAnulada = \App\Models\Facturacion\EstadoFactura::where('codigo', 'ANUL')->firstOrFail();

            // Evitar doble anulación
            if ($factura->estado?->codigo === 'ANUL') {
                return $factura;
            }

            // 🧾 Actualizar estado de factura y detalles
            $factura->update([
                'estado_id' => $estadoAnulada->id,
                'entregado' => 0,
            ]);

            foreach ($factura->detalles as $detalle) {
                $detalle->update([
                    'entregado' => 0,
                    'estado_id' => $estadoAnulada->id,
                ]);

                // 🔹 Si es un producto (Plan Separe), reingresar stock
                if ($detalle->tipo_item === 'plan_separe' && $detalle->referencia_id) {
                    $inventario = \App\Models\Inventario\Inventario::find($detalle->referencia_id);

                    if ($inventario) {
                        $stockAnterior = $inventario->stock;
                        $inventario->increment('stock', $detalle->cantidad);

                        // 📦 Registrar movimiento de inventario (entrada por devolución)
                        \App\Models\Inventario\MovimientoInventario::create([
                            'inventario_id'  => $inventario->id,
                            'tipo_movimiento'=> 'entrada',
                            'cantidad'       => $detalle->cantidad,
                            'stock_anterior' => $stockAnterior,
                            'stock_nuevo'    => $inventario->stock,
                            'costo_unitario' => $inventario->costo,
                            'motivo_id'      => DB::table('motivos_movimientos')
                                                ->where('codigo', 'entrada_plan_separe')
                                                ->value('id'),
                            'documento_referencia' => $factura->codigo,
                            'usuario_id'     => $usuarioId,
                            'observaciones'  => "Reingreso de producto por anulación de factura Plan Separe #{$factura->codigo}",
                            'created_at'     => now(),
                            'updated_at'     => now(),
                        ]);
                    }
                }
            }

            // 🧾 Registrar auditoría
            \App\Models\Facturacion\FacturaAuditoria::create([
                'factura_id' => $factura->id,
                'usuario_id' => $usuarioId,
                'accion'     => 'ANULAR',
                'detalle'    => 'Factura anulada automáticamente desde la anulación de un Plan Separe.',
                'created_at' => now(),
            ]);

            DB::commit();
            return $factura->fresh(['estado', 'detalles']);

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 📄 Listado de facturas (recalcula saldo y total pagado)
     */
    public function listarResumen(array $filters = [])
    {
        $query = Factura::query()
            ->with([
                'cliente', 
                'usuario', 
                'formaPago', 
                'estado', 
                'tipoVenta',
                'detalles.estado',  // ✅ Cargar detalles con su estado
                'pagos'             // ✅ Cargar pagos
            ])
            ->orderByDesc('fecha_emision');

        // 🔍 Filtros dinámicos
        if (!empty($filters['cliente_id'])) {
            $query->where('cliente_id', $filters['cliente_id']);
        }

        if (!empty($filters['usuario_id'])) {
            $query->where('usuario_id', $filters['usuario_id']);
        }

        if (!empty($filters['estado'])) {
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

        // 🔹 Filtro de prefactura
        if (isset($filters['es_prefactura'])) {
            $query->where('es_prefactura', (int)$filters['es_prefactura']);
        }

        // 📊 Paginación
        $perPage = $filters['per_page'] ?? 20;
        $facturas = $query->paginate($perPage);

        // 🔹 Recalcular totales usando las relaciones YA CARGADAS (no nuevas queries)
        $facturas->getCollection()->transform(function ($factura) {
            // ✅ Usar la colección ya cargada, no hacer nueva query
            $totalReal = $factura->detalles
                ->filter(fn($detalle) => $detalle->estado?->codigo !== 'ANUL')
                ->sum('total');

            // ✅ Usar la colección de pagos ya cargada
            $totalPagado = $factura->pagos
                ->filter(fn($pago) => $pago->estado !== 'anulado')
                ->sum('valor');

            // 🔹 Calcular saldo con el total actualizado
            $saldoPendiente = max($totalReal - $totalPagado, 0);

            // 🔹 Asignar valores calculados dinámicamente
            $factura->total = $totalReal;
            $factura->total_pagado = $totalPagado;
            $factura->saldo_pendiente = $saldoPendiente;

            return $factura;
        });

        return $facturas;
    }
}
