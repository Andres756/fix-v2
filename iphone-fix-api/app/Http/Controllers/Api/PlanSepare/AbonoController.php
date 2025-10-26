<?php

namespace App\Http\Controllers\Api\PlanSepare;

use App\Http\Controllers\Controller;
use App\Services\Facturacion\PlanSepareService;
use App\Http\Requests\PlanSepare\StoreAbonoPlanSepareRequest;
use App\Http\Resources\PlanSepare\AbonoPlanSepareResource;

class AbonoController extends Controller
{
    protected $service;

    public function __construct(PlanSepareService $service)
    {
        $this->service = $service;
    }

    /**
     * Registrar un nuevo abono a un plan separe
     */
    public function store(StoreAbonoPlanSepareRequest $request, $planId)
    {
        try {
            $data = $request->validated();
            $usuarioId = auth('sanctum')->id() ?? auth()->id() ?? 1;

            $abono = $this->service->registrarAbono($planId, $data, $usuarioId);

            return response()->json([
                'message' => 'Abono registrado correctamente.',
                'data' => new AbonoPlanSepareResource($abono)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }


    /**
     * Listar todos los abonos de un plan
     */
    public function index($planId)
    {
        $abonos = $this->service->obtenerAbonos($planId);
        return AbonoPlanSepareResource::collection($abonos);
    }
}
