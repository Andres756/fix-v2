<?php

namespace App\Http\Controllers\Api\Facturacion;

use App\Http\Controllers\Controller;
use App\Services\Facturacion\FacturacionService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Facturacion\Factura;
use App\Models\Facturacion\FacturaDetalle;
use App\Models\Facturacion\FacturaAuditoria;
use App\Services\Facturacion\AnulacionFacturaService;
use Illuminate\Support\Facades\Response;
use PDF; // usa barryvdh/laravel-dompdf
use Illuminate\Support\Facades\Cache;

class FacturacionController extends Controller
{
    protected $facturacionService;

    public function __construct(FacturacionService $facturacionService)
    {
        $this->facturacionService = $facturacionService;
    }

    /**
     * 📄 Listado de facturas
     */
    public function index(Request $request)
    {
        $filters = $request->all();
        $facturas = $this->facturacionService->listarResumen($filters);
        return response()->json($facturas);
    }
    /**
     * 🧾 Crear nueva factura (venta directa, servicio o plan separe)
     * Endpoint principal POST /api/facturacion/facturas
     */
    public function store(Request $request)
    {
        $request->validate([
            'origen' => 'required|string|in:venta,servicio,plan_separe',
            'cliente_id' => 'required|integer|exists:clientes,id',
            'forma_pago_id' => 'nullable|integer|exists:formas_pago,id',
            'observaciones' => 'nullable|string|max:255',
            'monto_recibido' => 'nullable|numeric|min:0',

            // Ítems (solo si origen = venta)
            'items' => 'required_if:origen,venta|array|min:1',
            'items.*.inventario_id' => 'required_if:origen,venta|integer|exists:inventarios,id',
            'items.*.cantidad' => 'required_if:origen,venta|numeric|min:1',
            'items.*.tipo_precio' => 'nullable|string|in:DET,MAY',

            // Orden de servicio (solo si origen = servicio)
            'orden_servicio_id' => 'required_if:origen,servicio|integer|exists:ordenes_servicio,id',

            // Control de entrega
            'entregado' => 'nullable|boolean',

            // Pagos
            'pagos' => 'nullable|array',
            'pagos.*.forma_pago_id' => 'required_with:pagos|integer|exists:formas_pago,id',
            'pagos.*.valor' => 'required_with:pagos|numeric|min:0.01',
        ]);

        try {
            $usuarioId = Auth::id() ?? $request->input('usuario_id');
            if (!$usuarioId) {
                throw ValidationException::withMessages([
                    'usuario' => 'No se pudo identificar el usuario que realiza la operación.'
                ]);
            }

            $origen = $request->input('origen');
            $entregado = $request->boolean('entregado', true);

            if ($origen === 'venta') {
                $resultado = $this->facturacionService->crearFacturaVenta($request->all(), $usuarioId);
            } elseif ($origen === 'servicio') {
                $resultado = $this->facturacionService->crearFacturaServicio(
                    (int)$request->input('orden_servicio_id'),
                    null,
                    $request->input('forma_pago_id'),
                    $usuarioId,
                    $request->input('observaciones'),
                    null,
                    $entregado
                );
            } elseif ($origen === 'plan_separe') {
                throw ValidationException::withMessages([
                    'origen' => 'Las facturas de plan separe se crean automáticamente al cierre del plan.'
                ]);
            }

            $factura = $resultado instanceof \Illuminate\Database\Eloquent\Model
                ? $resultado
                : ($resultado['factura'] ?? $resultado);

            return response()->json([
                'message' => 'Factura creada correctamente',
                'factura' => $factura,
                'vueltas' => $resultado['vueltas'] ?? 0,
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al crear la factura',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

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
            'equipos.repuestosInventario.inventario',
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
            $valorRepExt = (float)$eq->repuestosExternos->sum('costo');

            $valorTotalEquipo = $manoObra + $valorRepInv + $valorRepExt;
            if ($valorTotalEquipo <= 0) continue;

            \App\Models\Facturacion\FacturaDetalle::create([
                'factura_id'     => $factura->id,
                'tipo_item'      => self::TIPO_ITEM_OS_EQUIPO,
                'referencia_id'  => $eq->id,
                'descripcion'    => "Servicio técnico - Equipo {$eq->imei_serial}",
                'cantidad'       => 1,
                'valor_unitario' => $valorTotalEquipo,
                'descuento'      => 0,
                'impuesto'       => 0,
                'total'          => $valorTotalEquipo,
                'entregado'      => $entregado ? 1 : 0,
            ]);

            $subtotal += $valorTotalEquipo;

            $eq->update([
                'facturado' => 1,
                'entregado' => $entregado ? 1 : 0,
            ]);

            \App\Models\Facturacion\FacturaAuditoria::create([
                'factura_id' => $factura->id,
                'usuario_id' => $usuarioId,
                'accion'     => 'CREAR',
                'detalle'    => "Equipo ID {$eq->id} facturado. Entregado: " . ($entregado ? 'Sí' : 'No'),
                'created_at' => now(),
            ]);
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

        FacturaAuditoria::create([
            'factura_id' => $factura->id,
            'usuario_id' => $usuarioId,
            'accion'     => 'CREAR',
            'detalle'    => 'Factura creada por orden de servicio. Entregado: ' . ($entregado ? 'Sí' : 'No'),
            'created_at' => now(),
        ]);

        // ✅ Cerrar OS si todos los equipos ya están facturados
        $equiposPendientes = $os->equipos()->where('facturado', 0)->count();
        if ($equiposPendientes === 0) {
            $os->update(['estado' => 'cerrada']);

            \App\Models\Facturacion\FacturaAuditoria::create([
                'factura_id' => $factura->id,
                'usuario_id' => $usuarioId,
                'accion'     => 'EDITAR',
                'detalle'    => 'Orden de servicio cerrada automáticamente (todos los equipos facturados)',
                'created_at' => now(),
            ]);
        }

        return $factura->fresh(['cliente', 'detalles', 'estado']);
    }

    /**
     * 📄 Mostrar detalle de una factura (con totales y saldo recalculados)
     */
    public function show($id)
    {
        // 🔹 Cargar factura con todas sus relaciones relevantes
        $factura = Factura::with([
            'cliente',
            'usuario',
            'estado',
            'detalles.estado', // ✅ incluir estado del detalle
            'pagos.formaPago',
            'pagos.usuario'
        ])->findOrFail($id);

        // 🔹 Calcular total real (solo ítems activos)
        $totalReal = $factura->detalles()
            ->whereHas('estado', fn($q) => $q->where('codigo', '!=', 'ANUL'))
            ->sum('total');

        // 🔹 Calcular total pagado (solo pagos activos)
        $totalPagado = $factura->pagos()
            ->where('estado', '!=', 'anulado')
            ->sum('valor');

        // 🔹 Calcular saldo pendiente basado en el total real
        $saldoPendiente = max($totalReal - $totalPagado, 0);

        // 🔹 Asignar campos dinámicos (sin tocar base de datos)
        $factura->total = $totalReal;
        $factura->total_pagado = $totalPagado;
        $factura->saldo_pendiente = $saldoPendiente;

        // 🔹 Devolver respuesta limpia
        return response()->json([
            'id' => $factura->id,
            'codigo' => $factura->codigo,
            'cliente' => $factura->cliente,
            'usuario' => $factura->usuario,
            'estado' => $factura->estado,
            'fecha_emision' => $factura->fecha_emision,
            'subtotal' => $factura->subtotal,
            'total' => $totalReal, // ✅ actualizado
            'total_pagado' => $totalPagado,
            'saldo_pendiente' => $saldoPendiente,
            'detalles' => $factura->detalles,
            'pagos' => $factura->pagos,
        ]);
    }

    /**
     * 🚫 Anular una factura
     */
    public function anular($id)
    {
        \DB::beginTransaction();
        try {
            $usuarioId = \Auth::id();
            $factura = Factura::with(['detalles','estado'])->findOrFail($id);

            // 1) No permitir doble anulación
            if ($factura->estado?->codigo === 'ANUL') {
                return response()->json(['message' => 'La factura ya está anulada.'], 422);
            }

            // 2) Estado ANUL
            $estadoAnul = EstadoFactura::where('codigo', 'ANUL')->firstOrFail();

            // 3) Marcar factura anulada y no entregada
            $factura->update([
                'estado_id' => $estadoAnul->id,
                'entregado' => 0,
            ]);

            // 4) Revertir entrega de cada detalle (los triggers se encargarán del inventario solo si es "producto")
            FacturaDetalle::where('factura_id', $factura->id)
                ->update(['entregado' => 0]);

            // 5) Si es factura de OS: revertir flags del/los equipos asociados
            if ($factura->orden_servicio_id) {
                $equiposIds = FacturaDetalle::where('factura_id', $factura->id)
                    ->where('tipo_item', 'orden_servicio_equipo')
                    ->whereNotNull('referencia_id')
                    ->pluck('referencia_id')
                    ->unique()
                    ->toArray();

                if (!empty($equiposIds)) {
                    EquipoOrdenServicio::whereIn('id', $equiposIds)
                        ->update([
                            'entregado' => 0,
                            'facturado' => 0,
                        ]);
                }
            }

            // 6) Auditoría
            FacturaAuditoria::create([
                'factura_id' => $factura->id,
                'usuario_id' => $usuarioId,
                'accion'     => 'ANULAR',
                'detalle'    => 'Factura anulada. Se revirtió "entregado" en factura y detalles. En OS, equipos: entregado=0, facturado=0. Inventario lo manejan los triggers.',
                'created_at' => now(),
            ]);

            \DB::commit();

            return response()->json([
                'message' => 'Factura anulada correctamente.',
                'factura' => $factura->fresh(['estado'])
            ]);
        } catch (\Throwable $e) {
            \DB::rollBack();
            return response()->json([
                'message' => 'Error al anular la factura',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function anularAvanzado(Request $request, int $id, AnulacionFacturaService $anulacionService)
    {
        try {
            $resultado = $anulacionService->anularFacturaAvanzado($request->all(), $id);

            return response()->json([
                'message' => 'Anulación avanzada procesada correctamente',
                'data' => $resultado,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al procesar la anulación avanzada',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function verificarAnulacion($id)
    {
        $factura = Factura::find($id);

        if (!$factura) {
            return response()->json([
                'puede_anular' => false,
                'mensaje' => 'Factura no encontrada.'
            ], 404);
        }

        // 🚦 Ejemplo de lógica: no permitir anular si ya está anulada o entregada
        if ($factura->estado === 'anulada') {
            return response()->json([
                'puede_anular' => false,
                'mensaje' => 'La factura ya está anulada.'
            ], 200);
        }

        if ($factura->estado === 'entregada') {
            return response()->json([
                'puede_anular' => false,
                'mensaje' => 'No se puede anular una factura entregada.'
            ], 200);
        }

        // Si pasa todas las validaciones
        return response()->json([
            'puede_anular' => true,
            'mensaje' => 'La factura puede ser anulada.'
        ], 200);
    }

    /**
     *  Entrega
     */
    public function entregar(Request $request, int $id)
    {
        $request->validate([
            'entregas' => 'nullable|array',
            'entregas.*.detalle_id' => 'required_with:entregas|integer|exists:factura_detalle,id',
            'forzar' => 'nullable|boolean', // 👈 NUEVO parámetro
        ]);

        $usuarioId = Auth::id() ?? $request->input('usuario_id');
        $factura = Factura::with(['detalles', 'estado'])->findOrFail($id);

        // 🚫 No permitir entregas en facturas anuladas
        if ($factura->estado?->codigo === 'ANUL') {
            return response()->json(['message' => 'No se puede entregar una factura anulada.'], 422);
        }

        // 🚨 Validar si la factura está pagada (SOLO si no se fuerza)
        $forzar = $request->input('forzar', false);
        
        if ($factura->estado?->codigo !== '2' && !$forzar) {
            // Si la factura no está pagada Y no se está forzando, pedir confirmación
            return response()->json([
                'message' => 'La factura tiene saldo pendiente. ¿Está seguro de que desea entregar este producto?',
                'confirmar_entrega' => true
            ], 400);
        }

        DB::beginTransaction();
        try {
            $entregas = $request->input('entregas', []);
            $entregados = [];

            // Si se especifican ítems para entregar
            if (!empty($entregas)) {
                $ids = collect($entregas)->pluck('detalle_id')->toArray();
                FacturaDetalle::whereIn('id', $ids)
                    ->where('factura_id', $factura->id)
                    ->update(['entregado' => 1]);
                $entregados = $ids;
            } else {
                // Entrega total (marcar todos los ítems como entregados)
                FacturaDetalle::where('factura_id', $factura->id)
                    ->where('entregado', 0)
                    ->update(['entregado' => 1]);

                $entregados = $factura->detalles->where('entregado', 0)->pluck('id')->toArray();
            }

            // Si todos los detalles están entregados, marcar la factura como entregada
            $faltantes = FacturaDetalle::where('factura_id', $factura->id)
                ->where('entregado', 0)
                ->count();

            $factura->update(['entregado' => $faltantes === 0 ? 1 : 0]);

            // Auditoría
            FacturaAuditoria::create([
                'factura_id' => $factura->id,
                'usuario_id' => $usuarioId,
                'accion'     => 'EDITAR',
                'detalle'    => empty($entregas)
                    ? 'Factura entregada completamente.' . ($forzar ? ' (Forzado con saldo pendiente)' : '')
                    : sprintf('Entrega parcial de %d ítems (%s).%s',
                        count($entregados),
                        implode(',', $entregados),
                        $forzar ? ' (Forzado con saldo pendiente)' : ''
                    ),
                'created_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Entrega registrada correctamente.',
                'factura_id' => $factura->id,
                'entrega_total' => $faltantes === 0,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al registrar entrega',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 🛠️ Entregar equipos asociados a una factura de orden de servicio
     */
    public function entregarEquipos(Request $request, int $id)
    {
        $request->validate([
            'entregas' => 'nullable|array',
            'entregas.*.detalle_id' => 'required_with:entregas|integer|exists:factura_detalle,id',
        ]);

        $usuarioId = Auth::id() ?? $request->input('usuario_id');
        $factura = Factura::with(['detalles', 'estado'])->findOrFail($id);

        if ($factura->estado?->codigo === 'ANUL') {
            return response()->json(['message' => 'No se puede entregar una factura anulada.'], 422);
        }

        if (!$factura->orden_servicio_id) {
            return response()->json(['message' => 'Esta factura no pertenece a una orden de servicio.'], 422);
        }

        DB::beginTransaction();
        try {
            $entregas = $request->input('entregas', []);
            $entregados = [];

            if (!empty($entregas)) {
                $ids = collect($entregas)->pluck('detalle_id')->toArray();
                FacturaDetalle::whereIn('id', $ids)
                    ->where('factura_id', $factura->id)
                    ->update(['entregado' => 1]);
                $entregados = $ids;
            } else {
                FacturaDetalle::where('factura_id', $factura->id)
                    ->where('entregado', 0)
                    ->update(['entregado' => 1]);

                $entregados = $factura->detalles->where('entregado', 0)->pluck('id')->toArray();
            }

            // Actualizar equipos asociados
            $equiposIds = FacturaDetalle::where('factura_id', $factura->id)
                ->whereNotNull('referencia_id')
                ->where('tipo_item', 'orden_servicio_equipo')
                ->pluck('referencia_id')
                ->unique()
                ->toArray();

            if (!empty($equiposIds)) {
                \App\Models\OrdenServicio\EquipoOrdenServicio::whereIn('id', $equiposIds)
                    ->update(['entregado' => 1]);
            }

            // Marcar factura completa si todos entregados
            $faltantes = FacturaDetalle::where('factura_id', $factura->id)
                ->where('entregado', 0)
                ->count();

            $factura->update(['entregado' => $faltantes === 0 ? 1 : 0]);

            FacturaAuditoria::create([
                'factura_id' => $factura->id,
                'usuario_id' => $usuarioId,
                'accion'     => 'EDITAR',
                'detalle'    => empty($entregas)
                    ? 'Equipos entregados completamente.'
                    : sprintf('Entrega parcial de equipos (%s).', implode(',', $entregados)),
                'created_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Entrega de equipos registrada correctamente.',
                'factura_id' => $factura->id,
                'entrega_total' => $faltantes === 0,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al entregar equipos',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     *  Prefactura
     */
    public function prefacturarOS(Request $request, int $orden)
    {
        $request->validate([
            'equipos'        => 'nullable|array',
            'equipos.*'      => 'integer',
            'forma_pago_id'  => 'nullable|integer|exists:formas_pago,id',
            'observaciones'  => 'nullable|string|max:255',
            'entregado'      => 'nullable|boolean',
        ]);

        try {
            $usuarioId = Auth::id() ?? $request->input('usuario_id');
            if (!$usuarioId) {
                throw ValidationException::withMessages([
                    'usuario' => 'No se pudo identificar el usuario que realiza la operación.'
                ]);
            }

            // Si no viene el campo, por defecto entregado = true
            $entregado = $request->boolean('entregado', true);

            $factura = $this->facturacionService->crearFacturaServicio(
                $orden,
                null, // cliente viene de la OS
                $request->input('forma_pago_id'),
                $usuarioId,
                $request->input('observaciones'),
                $request->input('equipos', null),
                $entregado
            );

            return response()->json([
                'message' => $entregado
                    ? 'Factura generada y equipos marcados como entregados.'
                    : 'Factura generada. Equipos pendientes por entrega.',
                'factura' => $factura,
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al generar la factura de servicio',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function obtenerUrlImpresion(int $id)
    {
        try {
            $factura = \App\Models\Facturacion\Factura::findOrFail($id);

            // 🔹 Por ahora devolvemos la URL de ticket PDF
            $urlTicket = url("/api/facturacion/facturas/{$id}/ticket");

            return response()->json([
                'message' => 'URL de impresión generada correctamente',
                'url' => $urlTicket,
                'tipo' => 'ticket', // podrías cambiar a 'pdf' si necesitas formato A4
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al generar la URL de impresión',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function generarTicket(int $id)
    {
        try {
            $factura = Factura::with(['cliente', 'detalles', 'usuario', 'estado'])
                ->findOrFail($id);

            $empresa = \App\Models\Parametros\ParametroFacturacion::first();

            // Opción 2: Calcular altura dinámica basada en contenido
            $altoBase = 400; // Alto mínimo
            $altoPorProducto = 40; // Altura aproximada por producto
            $altoCalculado = $altoBase + ($factura->detalles->count() * $altoPorProducto);

            $pdf = \PDF::loadView('pdf.ticket', [
                'factura' => $factura,
                'empresa' => $empresa
            ])->setPaper([0, 0, 226.77, $altoCalculado], 'portrait');

            $nombreArchivo = "{$factura->codigo}_ticket.pdf";

            return Response::make($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "inline; filename=\"{$nombreArchivo}\""
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al generar el ticket de factura',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 📊 Resumen de facturación para dashboard
     */



    public function resumen()
    {
        return Cache::remember('facturacion_resumen', 60, function () {
            try {
                $hoy = now()->toDateString();
                $inicioMes = now()->startOfMonth()->toDateString();
                $finMes = now()->endOfMonth()->toDateString();

                // 1️⃣ Ventas del día
                $ventasDia = \App\Models\Facturacion\Factura::whereDate('fecha_emision', $hoy)
                    ->whereHas('estado', fn($q) => $q->where('codigo', '!=', 'ANUL'))
                    ->sum('total');

                // 2️⃣ Ventas del mes
                $ventasMes = \App\Models\Facturacion\Factura::whereBetween('fecha_emision', [$inicioMes, $finMes])
                    ->whereHas('estado', fn($q) => $q->where('codigo', '!=', 'ANUL'))
                    ->sum('total');

                // 3️⃣ Facturas pendientes (estado PEND)
                $pendientes = \App\Models\Facturacion\Factura::whereHas('estado', fn($q) => $q->where('codigo', 'PEND'))
                    ->count();

                // 4️⃣ Facturas anuladas este mes
                $anuladasMes = \App\Models\Facturacion\Factura::whereHas('estado', fn($q) => $q->where('codigo', 'ANUL'))
                    ->whereBetween('fecha_emision', [$inicioMes, $finMes])
                    ->count();

                return response()->json([
                    'message' => 'Resumen obtenido correctamente',
                    'data' => [
                        'ventas_dia' => $ventasDia,
                        'ventas_mes' => $ventasMes,
                        'facturas_pendientes' => $pendientes,
                        'anuladas_mes' => $anuladasMes,
                    ]
                ]);
            } catch (\Throwable $e) {
                return response()->json([
                    'message' => 'Error al obtener el resumen de facturación',
                    'error' => $e->getMessage(),
                ], 500);
            }
        });
    }
}
