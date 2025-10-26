<?php

namespace App\Http\Controllers\Api\PlanSepare;

use App\Http\Controllers\Controller;
use App\Services\Facturacion\PlanSepareService;
use App\Http\Requests\PlanSepare\StorePlanSepareRequest;
use App\Http\Resources\PlanSepare\PlanSepareResource;

class PlanSepareController extends Controller
{
    protected $service;

    public function __construct(PlanSepareService $service)
    {
        $this->service = $service;
    }

    /**
     * Listar todos los planes separe
     */
    public function index()
    {
        $planes = $this->service->listarPlanes();
        return PlanSepareResource::collection($planes);
    }

    /**
     * Crear un nuevo plan separe
     */
    public function store(StorePlanSepareRequest $request)
    {
        try {
            $data = $request->validated();
            $usuarioId = auth('sanctum')->id();

            if (!$usuarioId) {
                return response()->json([
                    'message' => 'No se detectó una sesión activa. Inicie sesión para crear un plan separe.'
                ], 401);
            }

            $plan = $this->service->crearPlan($data, $usuarioId);

            return response()->json([
                'message' => 'Plan Separe creado correctamente.',
                'data' => new PlanSepareResource(
                    $plan->load(['cliente', 'inventario', 'estado', 'abonos', 'logs'])
                )
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Mostrar detalle de un plan separe
     */
    public function show($id)
    {
        $plan = $this->service->listarPlanes()->firstWhere('id', $id);

        if (!$plan) {
            return response()->json(['message' => 'Plan no encontrado'], 404);
        }

        $plan->load(['abonos.formaPago', 'abonos.usuario', 'logs.usuario']);

        return new PlanSepareResource($plan);
    }
}
