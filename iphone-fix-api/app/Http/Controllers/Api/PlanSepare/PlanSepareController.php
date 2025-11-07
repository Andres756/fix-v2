<?php

namespace App\Http\Controllers\Api\PlanSepare;

use App\Http\Controllers\Controller;
use App\Services\Facturacion\PlanSepareService;
use App\Models\PlanSepare\PlanSepare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PlanSepareController extends Controller
{
    protected $planService;

    public function __construct(PlanSepareService $planService)
    {
        $this->planService = $planService;
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $planes = $this->planService->listar($perPage);
        return response()->json($planes);
    }

    public function show(int $id)
    {
        $plan = PlanSepare::with([
            'cliente',
            'inventario',
            'estado',
            'abonos.usuario',
            'devoluciones.usuario',
        ])->findOrFail($id);

        // ✅ Obtener el monto devuelto (última devolución registrada)
        $ultimaDevolucion = $plan->devoluciones->sortByDesc('created_at')->first();
        $plan->monto_devuelto = $ultimaDevolucion?->monto_devuelto ?? 0;

        // ✅ También podrías incluir datos del usuario que devolvió (si quieres mostrarlo)
        $plan->usuario_devolucion = $ultimaDevolucion?->usuario?->name ?? null;

        return response()->json($plan);
    }


    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|integer|exists:clientes,id',
            'inventario_id' => 'required|integer|exists:inventarios,id',
            'precio_total' => 'required|numeric|min:0',
            'porcentaje_minimo' => 'required|numeric|min:10|max:100',
            'observaciones' => 'nullable|string|max:255',
        ]);

        $usuarioId = Auth::id();

        try {
            $plan = $this->planService->crearPlan($request->all(), $usuarioId);
            return response()->json([
                'message' => 'Plan separe creado correctamente.',
                'plan' => $plan,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al crear el plan separe.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function anular(Request $request, int $id)
    {
        $validated = $request->validate([
            'motivo' => 'nullable|string|max:255',
            'motivo_anulacion_id' => 'nullable|exists:motivos_anulacion_plansepare,id',
            'porcentaje_devolucion' => 'nullable|numeric|min:0|max:100',
            'forma_pago_id' => 'nullable|integer|exists:formas_pago,id',
            'observaciones' => 'nullable|string|max:255',
        ]);

        $usuarioId = Auth::id();

        try {
            $resultado = $this->planService->anularPlan($id, $validated, $usuarioId);
            return response()->json($resultado);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al anular el plan separe.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 🔁 Reasignar plan separe (estado REA)
     */
    public function reasignar(Request $request, int $id)
    {
        $request->validate([
            'nuevo_inventario_id' => 'required|integer|exists:inventarios,id',
            'precio_total' => 'nullable|numeric|min:0',
            'porcentaje_minimo' => 'nullable|numeric|min:10|max:100',
        ]);

        $usuarioId = Auth::id();

        try {
            $plan = $this->planService->reasignarPlan(
                $id,
                $request->nuevo_inventario_id,
                $usuarioId,
                $request->input('precio_total'),
                $request->input('porcentaje_minimo')
            );

            return response()->json([
                'message' => 'Plan separe reasignado correctamente.',
                'plan' => $plan,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al reasignar el plan separe.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
