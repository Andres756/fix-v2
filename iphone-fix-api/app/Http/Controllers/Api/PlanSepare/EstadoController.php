<?php

namespace App\Http\Controllers\Api\PlanSepare;

use App\Http\Controllers\Controller;
use App\Models\PlanSepare\PlanSepare;
use App\Models\PlanSepare\EstadoPlanSepare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EstadoController extends Controller
{
    /**
     * 🟡 Cambiar el estado de un plan manualmente (uso administrativo)
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'estado_id' => 'required|integer|exists:estados_plan_separe,id',
            'observaciones' => 'nullable|string|max:255',
        ]);

        $usuarioId = Auth::id();

        DB::beginTransaction();
        try {
            $plan = PlanSepare::with('inventario')->findOrFail($id);
            $estado = EstadoPlanSepare::findOrFail($request->estado_id);

            // Cambiar estado
            $plan->update([
                'estado_id' => $estado->id,
            ]);

            // Si el nuevo estado implica liberar el inventario
            if (in_array(strtoupper($estado->codigo), ['ANUL', 'DEV'])) {
                if ($plan->inventario && $plan->inventario->tipo_inventario == 1) {
                    $plan->inventario->update(['reservado' => 0]);
                }
            }

            // Registrar auditoría
            DB::table('plan_separe_auditoria')->insert([
                'plan_separe_id' => $plan->id,
                'usuario_id' => $usuarioId,
                'accion' => 'EDITAR',
                'detalle' => "Cambio manual de estado a {$estado->nombre}. Observaciones: {$request->observaciones}",
                'created_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => "Estado del plan separe actualizado manualmente a {$estado->nombre}.",
                'plan' => $plan->fresh(['cliente', 'estado', 'inventario'])
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al cambiar el estado.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 💸 Marcar plan como DEV (devolución manual)
     */
    public function devolver(int $id)
    {
        $usuarioId = Auth::id();

        DB::beginTransaction();
        try {
            $plan = PlanSepare::with('inventario')->findOrFail($id);
            $estadoDev = EstadoPlanSepare::where('codigo', 'DEV')->firstOrFail();

            $plan->update(['estado_id' => $estadoDev->id]);

            if ($plan->inventario && $plan->inventario->tipo_inventario == 1) {
                $plan->inventario->update(['reservado' => 0]);
            }

            DB::table('plan_separe_auditoria')->insert([
                'plan_separe_id' => $plan->id,
                'usuario_id' => $usuarioId,
                'accion' => 'EDITAR',
                'detalle' => 'Cambio manual a DEV (Devolución al cliente)',
                'created_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Plan separe marcado como DEV correctamente.',
                'plan' => $plan->fresh(['cliente', 'estado', 'inventario'])
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al marcar devolución.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
