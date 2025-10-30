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
     * 
     * Permite registrar factura, pagos y calcular vueltas.
     */
    public function store(Request $request)
    {
        // ✅ Reglas condicionales por origen
        $request->validate([
            'origen' => 'required|string|in:venta,servicio,plan_separe',

            // Venta directa: exige items
            'items' => 'required_if:origen,venta|array|min:1',
            'items.*.inventario_id' => 'required_if:origen,venta|integer|exists:inventarios,id',
            'items.*.cantidad' => 'required_if:origen,venta|numeric|min:1',
            'items.*.tipo_precio' => 'nullable|string|in:DET,MAY',

            // Servicio: NO exige items; sí exige OS
            'orden_servicio_id' => 'required_if:origen,servicio|integer|exists:ordenes_servicio,id',

            // Comunes
            'cliente_id' => 'required|integer|exists:clientes,id',
            'forma_pago_id' => 'nullable|integer|exists:formas_pago,id',
            'monto_recibido' => 'nullable|numeric|min:0',
            'pagos' => 'nullable|array',
            'pagos.*.forma_pago_id' => 'required_with:pagos|integer|exists:formas_pago,id',
            'pagos.*.valor' => 'required_with:pagos|numeric|min:0.01',
            'entregado' => 'nullable|boolean',
            'observaciones' => 'nullable|string|max:255',
        ]);

        try {
            $usuarioId = Auth::id() ?? $request->input('usuario_id');
            if (!$usuarioId) {
                throw ValidationException::withMessages([
                    'usuario' => 'No se pudo identificar el usuario que realiza la operación.'
                ]);
            }

            $origen = $request->input('origen');

            if ($origen === 'venta') {
                $resultado = $this->facturacionService->crearFacturaVenta($request->all(), $usuarioId);

            } elseif ($origen === 'servicio') {
                $resultado = $this->facturacionService->crearFacturaServicio(
                    (int)$request->input('orden_servicio_id'),
                    (int)$request->input('cliente_id'),
                    $request->input('forma_pago_id'),
                    $usuarioId,
                    $request->input('observaciones')
                );

            } else { // plan_separe
                throw ValidationException::withMessages([
                    'origen' => 'Las facturas de plan separe se crean automáticamente al cierre del plan.'
                ]);
            }

            // $resultado puede ser Modelo (Eloquent) o array
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
}
