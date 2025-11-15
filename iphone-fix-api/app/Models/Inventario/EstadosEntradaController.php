<?php
// app/Http/Controllers/Api/Parametros/Inventario/EstadosEntradaController.php

namespace App\Http\Controllers\Api\Parametros\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\EstadoEntrada;
use Illuminate\Http\Request;

class EstadosEntradaController extends Controller
{
    /**
     * Listar todos los estados de entrada
     */
    public function index()
    {
        $estados = EstadoEntrada::activos()
            ->ordenados()
            ->get();

        return response()->json(['data' => $estados]);
    }

    /**
     * Devolver opciones simplificadas (para selects)
     */
    public function options()
    {
        $estados = EstadoEntrada::activos()
            ->ordenados()
            ->get(['id', 'nombre', 'codigo', 'color']);

        return response()->json($estados);
    }

    /**
     * Obtener un estado específico
     */
    public function show($id)
    {
        $estado = EstadoEntrada::findOrFail($id);
        return response()->json(['data' => $estado]);
    }
}