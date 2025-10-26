<?php

namespace App\Http\Controllers\Api\PlanSepare;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlanSepare\StoreDevolucionRequest;
use App\Http\Resources\PlanSepare\DevolucionPlanSepareResource;
use App\Models\PlanSepare\PlanSepare;
use App\Models\PlanSepare\DevolucionPlanSepare;
use App\Models\PlanSepare\EstadoPlanSepare;
use Illuminate\Support\Facades\DB;

class DevolucionController extends Controller
{
    // 🔹 Listar devoluciones de un plan
    public function index($planId)
    {
        $plan = PlanSepare::findOrFail($planId);

        $devoluciones = $plan->devoluciones()
            ->orderByDesc('fecha_devolucion')
            ->get();

        return DevolucionPlanSepareResource::collection($devoluciones);
    }

    // 🔹 Registrar una nueva devolución
    public function store(StoreDevolucionRequest $request, $planId)
    {
        $usuarioId = auth('sanctum')->id();
        if (!$usuarioId) {
            return response()->json(['message' => 'Sesión no autenticada.'], 401);
        }

        $plan = PlanSepare::findOrFail($planId);

        // 💰 Total abonado
        $totalAbonado = DB::table('abonos_plan_separe')
            ->where('plan_separe_id', $plan->id)
            ->sum('valor');

        // 💰 Validación de monto
        if ($request->valor_devolucion > $totalAbonado) {
            return response()->json([
                'message' => "El valor de devolución no puede superar el total abonado ({$totalAbonado})."
            ], 422);
        }

        // ⚙️ Registrar devolución
        $devolucion = DevolucionPlanSepare::create([
            'plan_separe_id'        => $plan->id,
            'valor_devolucion'      => $request->valor_devolucion,
            'porcentaje_devolucion' => $totalAbonado > 0
                ? round(($request->valor_devolucion / $totalAbonado) * 100, 2)
                : 0,
            'motivo'        => $request->motivo,
            'usuario_id'    => $usuarioId,
            'observaciones' => $request->observaciones,
            'fecha_devolucion' => now(),
        ]);

        // 🔄 Cambiar estado del plan a DEV
        $estadoDev = EstadoPlanSepare::where('codigo', 'DEV')->first();
        if ($estadoDev) {
            $plan->update(['estado_id' => $estadoDev->id]);
        }

        // 🔓 Liberar inventario
        if ($plan->inventario_id) {
            DB::table('inventarios')->where('id', $plan->inventario_id)->update(['reservado' => 0]);
        }

        return response()->json([
            'message' => 'Devolución registrada correctamente.',
            'data'    => new DevolucionPlanSepareResource($devolucion)
        ]);
    }
}
