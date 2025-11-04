<?php

namespace App\Http\Controllers\Api\PlanSepare;

use App\Http\Controllers\Controller;
use App\Models\PlanSepare\DevolucionPlanSepare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DevolucionController extends Controller
{
    /**
     * 📋 Listar devoluciones
     */
    public function index(int $planId)
    {
        $devoluciones = DevolucionPlanSepare::where('plan_separe_id', $planId)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($devoluciones);
    }

    /**
     * 💰 Registrar devolución manual (casos excepcionales)
     */
    public function store(Request $request, int $planId)
    {
        $request->validate([
            'monto_total' => 'required|numeric|min:0',
            'monto_devuelto' => 'required|numeric|min:0',
            'porcentaje_devolucion' => 'nullable|integer|min:0|max:100',
            'forma_pago_id' => 'required|integer|exists:formas_pago,id',
            'observaciones' => 'nullable|string|max:255',
        ]);

        $usuarioId = Auth::id();

        $devolucion = DevolucionPlanSepare::create([
            'plan_separe_id' => $planId,
            'monto_total' => $request->monto_total,
            'monto_devuelto' => $request->monto_devuelto,
            'porcentaje_devolucion' => $request->porcentaje_devolucion ?? 100,
            'forma_pago_id' => $request->forma_pago_id,
            'usuario_id' => $usuarioId,
            'observaciones' => $request->observaciones,
        ]);

        return response()->json([
            'message' => 'Devolución registrada correctamente.',
            'devolucion' => $devolucion
        ], 201);
    }
}
