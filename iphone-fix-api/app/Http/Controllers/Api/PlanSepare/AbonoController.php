<?php

namespace App\Http\Controllers\Api\PlanSepare;

use App\Http\Controllers\Controller;
use App\Services\Facturacion\PlanSepareService;
use App\Models\PlanSepare\AbonoPlanSepare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbonoController extends Controller
{
    protected $planService;

    public function __construct(PlanSepareService $planService)
    {
        $this->planService = $planService;
    }

    /**
     * 💵 Registrar nuevo abono
     */
    public function store(Request $request, int $planId)
    {
        $request->validate([
            'valor' => 'required|numeric|min:1000',
            'forma_pago_id' => 'required|integer|exists:formas_pago,id',
        ]);

        $usuarioId = Auth::id();

        try {
            $resultado = $this->planService->registrarAbono($planId, $request->all(), $usuarioId);
            return response()->json([
                'message' => 'Abono registrado correctamente.',
                'data' => $resultado,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al registrar el abono.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 📋 Listar abonos de un plan
     */
    public function index(int $planId)
    {
        $abonos = AbonoPlanSepare::where('plan_separe_id', $planId)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($abonos);
    }
}
