<?php

namespace App\Http\Controllers\Api\Parametros\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\MotivoMovimiento;
use Illuminate\Http\Request;

class MotivosIngresoController extends Controller
{
    /**
     * Listar todos los motivos de ingreso (tipo = entrada)
     */
    public function index()
    {
        $motivos = MotivoMovimiento::activos()
            ->entradas()
            ->ordenados()
            ->get();

        return response()->json(['data' => $motivos]);
    }

    /**
     * Devolver opciones simplificadas (para selects, combos, etc.)
     */
    public function options()
    {
        $motivos = MotivoMovimiento::activos()
            ->entradas()
            ->ordenados()
            ->get(['id', 'nombre']);

        return response()->json($motivos);
    }
}
