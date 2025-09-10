<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrdenServicio\EquipoOrdenServicio;
use App\Models\OrdenServicio\TareaEquipo;
use Illuminate\Http\Request;

class TecnicoController extends Controller
{
    /**
     * Listar técnicos
     */
    public function index(Request $request)
    {
        $query = \App\Models\User::query();

        if ($request->filled('q')) {
            $query->where('name', 'like', "%{$request->q}%");
        }

        $tecnicos = $query->paginate($request->get('per_page', 15));

        return \App\Http\Resources\TecnicoResource::collection($tecnicos);
    }

    /**
     * Equipos asignados a un técnico
     */
    public function equiposAsignados($tecnicoId, Request $request)
    {
        $query = EquipoOrdenServicio::where('tecnico_asignado', $tecnicoId)
            ->with('tareas');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $equipos = $query->get()->map(function ($equipo) {
            $totalTareas = $equipo->tareas->sum('costo_aplicado');
            $ganancia = 0;

            if ($equipo->comision_habilitada) {
                if ($equipo->tipo_comision === 'porcentaje') {
                    $ganancia = $totalTareas * ($equipo->valor_comision / 100);
                } else {
                    $ganancia = $equipo->valor_comision;
                }
            }

            return [
                'id' => $equipo->id,
                'marca' => $equipo->marca,
                'modelo' => $equipo->modelo,
                'imei_serial' => $equipo->imei_serial,
                'estado' => $equipo->estado,
                'valor_estimado' => $equipo->valor_estimado,
                'fecha_estimada_entrega' => $equipo->fecha_estimada_entrega,
                'tareas' => $equipo->tareas->map(fn($t) => [
                    'id' => $t->id,
                    'nombre' => $t->nombre,
                    'costo_aplicado' => $t->costo_aplicado,
                    'estado' => $t->estado,
                ]),
                'comision' => [
                    'tipo' => $equipo->tipo_comision,
                    'valor' => $equipo->valor_comision,
                    'ganancia' => $ganancia,
                ],
            ];
        });

        return response()->json($equipos);
    }

    /**
     * Resumen de ganancias del técnico
     */
    public function ganancias($tecnicoId)
    {
        $equipos = EquipoOrdenServicio::where('tecnico_asignado', $tecnicoId)
            ->with('tareas')
            ->get();

        $totalGanado = 0;
        $resumenEquipos = [];

        foreach ($equipos as $equipo) {
            $totalTareas = $equipo->tareas->sum('costo_aplicado');
            $ganancia = 0;

            if ($equipo->comision_habilitada) {
                if ($equipo->tipo_comision === 'porcentaje') {
                    $ganancia = $totalTareas * ($equipo->valor_comision / 100);
                } else {
                    $ganancia = $equipo->valor_comision;
                }
            }

            $totalGanado += $ganancia;

            $resumenEquipos[] = [
                'equipo_id' => $equipo->id,
                'modelo' => $equipo->modelo,
                'total_tareas' => $totalTareas,
                'comision' => [
                    'tipo' => $equipo->tipo_comision,
                    'valor' => $equipo->valor_comision,
                    'ganancia' => $ganancia,
                ],
            ];
        }

        return response()->json([
            'total_ganado' => $totalGanado,
            'equipos' => $resumenEquipos,
        ]);
    }
}
