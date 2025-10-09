<?php

namespace App\Http\Controllers\Api\Parametros\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Parametros\MotivoIngreso;
use Illuminate\Http\Request;

class MotivosIngresoController extends Controller
{
    public function index()
    {
        $motivos = MotivoIngreso::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return response()->json(['data' => $motivos]);
    }

    public function options()
    {
        $motivos = MotivoIngreso::where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return response()->json($motivos);
    }
}