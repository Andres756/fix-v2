<?php

namespace App\Http\Controllers\Api\PlanSepare;

use App\Http\Controllers\Controller;
use App\Models\PlanSepare\MotivoAnulacionPlanSepare;

class MotivoAnulacionPlanSepareController extends Controller
{
    public function index()
    {
        // Solo activos, orden alfabético
        return response()->json(
            MotivoAnulacionPlanSepare::query()
                ->where('activo', 1)
                ->orderBy('nombre')
                ->get(['id','nombre','descripcion'])
        );
    }
}
