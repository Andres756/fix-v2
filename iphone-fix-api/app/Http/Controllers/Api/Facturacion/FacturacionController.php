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

        \App\Models\Facturacion\FacturaAuditoria::create([
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
     * 🧾 Mostrar una factura específica
     */
    public function show($id)
    {
        try {
            $factura = \App\Models\Facturacion\Factura::with([
                'cliente', 'usuario', 'estado', 'formaPago', 'detalles', 'pagos', 'auditorias'
            ])->findOrFail($id);

            return response()->json($factura);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'No se pudo obtener la factura',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * 🚫 Anular una factura
     */
    public function anular($id)
    {
        try {
            $usuarioId = Auth::id();
            $factura = $this->facturacionService->anularFactura($id, $usuarioId);
            return response()->json([
                'message' => 'Factura anulada correctamente',
                'factura' => $factura
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al anular la factura',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     *  Entrega
     */
    public function entregar(Request $request, int $id)
    {
        $request->validate([
            'entregas' => 'nullable|array',
            'entregas.*.detalle_id' => 'required_with:entregas|integer|exists:factura_detalle,id',
            'forzar' => 'nullable|boolean',
        ]);

        $usuario = Auth::user();
        $usuarioId = $usuario->id ?? $request->input('usuario_id');

        $factura = Factura::with(['detalles', 'estado'])->findOrFail($id);
        $estado = $factura->estado?->codigo;

        // 🚫 No permitir entregas de facturas anuladas
        if ($estado === 'ANUL') {
            return response()->json(['message' => 'No se puede entregar una factura anulada.'], 422);
        }

        $pagado = $factura->total_pagado >= $factura->total;

        // ⚠️ Antes: Si no estaba pagada, solo un admin podía forzar.
        // 🔹 AHORA: Permitimos entregas siempre (sin validar rol)
        /*
        if (!$pagado) {
            $forzar = $request->boolean('forzar', false);

            if (!$forzar) {
                return response()->json([
                    'message' => 'La factura no está completamente pagada. Solo un administrador puede autorizar la entrega parcial.',
                    'requiere_confirmacion' => true
                ], 403);
            }

            if ($usuario->role !== 'admin') {
                throw ValidationException::withMessages([
                    'autorizacion' => 'Solo un usuario administrador puede entregar una factura no pagada.'
                ]);
            }
        }
        */

        DB::beginTransaction();
        try {
            $entregas = $request->input('entregas', []);
            $entregados = [];

            // 🧩 Entrega parcial (solo ítems seleccionados)
            if (!empty($entregas)) {
                foreach ($entregas as $ent) {
                    $detalle = FacturaDetalle::where('id', $ent['detalle_id'])
                        ->where('factura_id', $factura->id)
                        ->firstOrFail();

                    if (!$detalle->entregado) {
                        $detalle->update(['entregado' => 1]);
                        $entregados[] = $detalle->id;
                    }
                }
            } else {
                // 🧩 Entregar todos los pendientes
                FacturaDetalle::where('factura_id', $factura->id)
                    ->where('entregado', 0)
                    ->update(['entregado' => 1]);

                $entregados = $factura->detalles->where('entregado', 0)->pluck('id')->toArray();
            }

            // 🧮 Si todos los detalles están entregados, marcar factura completa
            $faltantes = FacturaDetalle::where('factura_id', $factura->id)
                ->where('entregado', 0)
                ->count();

            if ($faltantes === 0) {
                $factura->update(['entregado' => 1]);
            }

            // 🧾 Registrar auditoría
            FacturaAuditoria::create([
                'factura_id' => $factura->id,
                'usuario_id' => $usuarioId,
                'accion' => 'EDITAR',
                'detalle' => empty($entregas)
                    ? 'Factura entregada completamente.'
                    : sprintf('Entrega parcial de %d productos (IDs: %s).',
                        count($entregados),
                        implode(',', $entregados)
                    ),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Entrega registrada correctamente.',
                'factura_id' => $factura->id,
                'entregados' => $entregados,
                'entrega_total' => $faltantes === 0
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
}
