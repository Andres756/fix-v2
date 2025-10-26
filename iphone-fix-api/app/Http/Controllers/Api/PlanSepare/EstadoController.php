<?php

namespace App\Http\Controllers\Api\PlanSepare;

use App\Http\Controllers\Controller;
use App\Services\Facturacion\PlanSepareService;
use App\Http\Requests\PlanSepare\UpdateEstadoPlanSepareRequest;
use App\Http\Resources\PlanSepare\PlanSepareResource;

class EstadoController extends Controller
{
    protected $service;

    public function __construct(PlanSepareService $service)
    {
        $this->service = $service;
    }

    /**
     * Cambiar el estado de un plan separe
     */
    public function update(UpdateEstadoPlanSepareRequest $request, $id)
    {
        try {
            $usuarioId = auth('sanctum')->id();

            if (!$usuarioId) {
                return response()->json([
                    'message' => 'No se detectó una sesión activa. Inicie sesión nuevamente.'
                ], 401);
            }

            // Buscar el plan
            $plan = \App\Models\PlanSepare\PlanSepare::find($id);

            if (!$plan) {
                return response()->json([
                    'message' => "No se encontró el Plan Separe con ID {$id}."
                ], 404);
            }

            // Verificar estado_id
            if (!$request->filled('estado_id')) {
                return response()->json([
                    'message' => 'Debe enviar el campo estado_id.'
                ], 422);
            }

            // Buscar el estado manualmente
            $estado = \App\Models\PlanSepare\EstadoPlanSepare::find($request->estado_id);
            if (!$estado) {
                return response()->json([
                    'message' => "El estado con ID {$request->estado_id} no existe."
                ], 422);
            }

            // Cambiar el estado
            $plan = $this->service->cambiarEstado(
                $plan->id,
                $estado->id,
                $request->observaciones,
                $usuarioId
            );

            return response()->json([
                'message' => 'Estado del Plan Separe actualizado correctamente.',
                'data' => $plan->load(['cliente', 'estado'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }


    public function devolver($id)
    {
        try {
            $usuarioId = auth('sanctum')->id();

            if (!$usuarioId) {
                return response()->json([
                    'message' => 'No se detectó una sesión activa. Inicie sesión nuevamente.'
                ], 401);
            }

            // 🔎 Buscar el plan
            $plan = \App\Models\PlanSepare\PlanSepare::find($id);
            if (!$plan) {
                return response()->json([
                    'message' => "No se encontró el Plan Separe con ID {$id}."
                ], 404);
            }

            // 🔎 Buscar el estado DEV (devolución)
            $estadoDev = \App\Models\PlanSepare\EstadoPlanSepare::where('codigo', 'DEV')->first();
            if (!$estadoDev) {
                return response()->json([
                    'message' => 'El estado DEV no está registrado en la tabla estados_plan_separe.'
                ], 500);
            }

            // ⚙️ Cambiar el estado a DEV usando el servicio
            $plan = $this->service->cambiarEstado($plan->id, $estadoDev->id, 'Devolución del dinero al cliente', $usuarioId);

            // ⚙️ Liberar inventario (reservado = 0)
            if ($plan->inventario_id) {
                \DB::table('inventarios')->where('id', $plan->inventario_id)->update(['reservado' => 0]);
            }

            return response()->json([
                'message' => 'Plan Separe marcado como devuelto correctamente.',
                'data' => $plan->load(['cliente', 'estado', 'inventario'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

}
