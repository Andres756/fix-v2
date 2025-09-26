<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TecnicoResource;
use App\Models\OrdenServicio\EquipoOrdenServicio;
use App\Models\OrdenServicio\TareaEquipo;
use App\Models\OrdenServicio\TareaEquipoHistorial;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TecnicoController extends Controller
{
    /**
     * Listar técnicos (todos los usuarios - sin filtro por rol)
     */
    public function index(Request $request): JsonResponse
    {
        $query = \App\Models\User::query();

        if ($request->filled('q')) {
            $query->where('name', 'like', "%{$request->q}%");
        }

        $tecnicos = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => TecnicoResource::collection($tecnicos->items()),
            'pagination' => [
                'current_page' => $tecnicos->currentPage(),
                'last_page' => $tecnicos->lastPage(),
                'per_page' => $tecnicos->perPage(),
                'total' => $tecnicos->total(),
            ]
        ]);
    }

    /**
     * Dashboard del técnico con métricas
     */
    public function dashboard($tecnicoId): JsonResponse
    {
        $equipos = EquipoOrdenServicio::where('tecnico_asignado', $tecnicoId)
            ->with(['tareas.tipoTrabajo', 'orden.cliente'])
            ->get();

        // Calcular métricas del dashboard
        $stats = [
            'total_equipos' => $equipos->count(),
            'equipos_pendientes' => $equipos->where('estado', '!=', 'completado')->count(),
            'equipos_completados' => $equipos->where('estado', 'completado')->count(),
            'equipos_en_proceso' => $equipos->where('estado', 'en_proceso')->count(),
            
            'total_tareas' => $equipos->sum(fn($e) => $e->tareas->count()),
            'tareas_pendientes' => $equipos->sum(fn($e) => $e->tareas->where('estado', 'pendiente')->count()),
            'tareas_en_proceso' => $equipos->sum(fn($e) => $e->tareas->where('estado', 'en_proceso')->count()),
            'tareas_completadas' => $equipos->sum(fn($e) => $e->tareas->where('estado', 'completada')->count()),
        ];

        return response()->json([
            'stats' => $stats,
            'equipos' => $equipos->map(function ($equipo) {
                return $this->formatEquipoData($equipo);
            })
        ]);
    }

    /**
     * Equipos asignados a un técnico (método original mejorado)
     */
    public function equiposAsignados($tecnicoId, Request $request): JsonResponse
    {
        $query = EquipoOrdenServicio::where('tecnico_asignado', $tecnicoId)
            ->with(['tareas.tipoTrabajo', 'orden.cliente']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $equipos = $query->get()->map(function ($equipo) {
            return $this->formatEquipoData($equipo);
        });

        return response()->json($equipos);
    }

    /**
     * Resumen de ganancias del técnico
     */
    public function ganancias($tecnicoId): JsonResponse
    {
        $equipos = EquipoOrdenServicio::where('tecnico_asignado', $tecnicoId)
            ->with('tareas')
            ->get();

        $totalGanado = 0;
        $resumenEquipos = [];

        foreach ($equipos as $equipo) {
            $totalTareas = $equipo->tareas->sum('costo_aplicado');
            $ganancia = $this->calcularGanancia($equipo, $totalTareas);
            $totalGanado += $ganancia;

            if ($ganancia > 0) { // Solo mostrar equipos con ganancia
                $resumenEquipos[] = [
                    'equipo_os_id' => $equipo->id,
                    'marca' => $equipo->marca,
                    'modelo' => $equipo->modelo,
                    'total_tareas_valor' => $totalTareas,
                    'comision' => [
                        'tipo' => $equipo->tipo_comision,
                        'valor' => $equipo->valor_comision,
                        'ganancia' => $ganancia,
                    ],
                ];
            }
        }

        return response()->json([
            'total_ganado' => $totalGanado,
            'equipos_con_comision' => count($resumenEquipos),
            'equipos' => $resumenEquipos,
        ]);
    }

    /**
     * Cambiar el estado de una tarea asignada a un técnico
     */
    public function actualizarEstadoTarea(Request $request, $tecnicoId, $tareaId): JsonResponse
    {
        $request->validate([
            'estado' => 'required|in:pendiente,en_proceso,completada,cancelada',
        ]);

        $tarea = TareaEquipo::findOrFail($tareaId);

        // Validar que el técnico tenga asignado el equipo
        $equipo = EquipoOrdenServicio::where('id', $tarea->equipo_os_id)
            ->where('tecnico_asignado', $tecnicoId)
            ->firstOrFail();

        $estadoAnterior = $tarea->estado;
        $tarea->estado = $request->estado;
        $tarea->save();

        // Guardar historial
        TareaEquipoHistorial::create([
            'tarea_equipo_id' => $tarea->id,
            'tecnico_id' => $tecnicoId,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $tarea->estado,
            'cambiado_en' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado de la tarea actualizado correctamente',
            'tarea' => [
                'id' => $tarea->id,
                'nombre' => $tarea->nombre ?? 'Tarea sin nombre',
                'estado' => $tarea->estado,
                'estado_anterior' => $estadoAnterior,
            ]
        ]);
    }

    /**
     * Historial de una tarea asignada a un técnico
     */
    public function historialTarea($tecnicoId, $tareaId): JsonResponse
    {
        $tarea = TareaEquipo::findOrFail($tareaId);

        // Validar que la tarea pertenezca a un equipo del técnico
        $equipo = EquipoOrdenServicio::where('id', $tarea->equipo_os_id)
            ->where('tecnico_asignado', $tecnicoId)
            ->firstOrFail();

        $historial = TareaEquipoHistorial::where('tarea_equipo_id', $tarea->id)
            ->with('tecnico:id,name')
            ->orderBy('cambiado_en', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'estado_anterior' => $item->estado_anterior,
                    'estado_nuevo' => $item->estado_nuevo,
                    'cambiado_en' => $item->cambiado_en->format('Y-m-d H:i:s'),
                    'tecnico' => [
                        'id' => $item->tecnico->id ?? null,
                        'nombre' => $item->tecnico->name ?? 'Sistema',
                    ]
                ];
            });

        return response()->json($historial);
    }

    /**
     * Formatear datos del equipo de manera consistente
     */
    private function formatEquipoData($equipo): array
    {
        $ganancia = 0;
        $totalTareas = $equipo->tareas->sum('costo_aplicado');
        
        if ($equipo->comision_habilitada) {
            $ganancia = $this->calcularGanancia($equipo, $totalTareas);
        }

        return [
            'id' => $equipo->id,
            'marca' => $equipo->marca,
            'modelo' => $equipo->modelo,
            'imei_serial' => $equipo->imei_serial,
            'estado' => $equipo->estado,
            'fecha_estimada_entrega' => $equipo->fecha_estimada_entrega,
            
            // Información del cliente/orden
            'cliente' => $equipo->orden->cliente->nombre ?? 'Sin cliente',
            'orden_codigo' => $equipo->orden->codigo_orden ?? 'Sin código',
            
            // Tareas (SIN COSTOS - solo descripción y estado)
            'tareas' => $equipo->tareas->map(fn($t) => [
                'id' => $t->id,
                'nombre' => $t->tipoTrabajo->nombre ?? 'Tarea sin descripción',
                'estado' => $t->estado,
                // NO incluimos costo_aplicado aquí
            ]),
            
            // Resumen de tareas
            'resumen_tareas' => [
                'total' => $equipo->tareas->count(),
                'pendientes' => $equipo->tareas->where('estado', 'pendiente')->count(),
                'en_proceso' => $equipo->tareas->where('estado', 'en_proceso')->count(),
                'completadas' => $equipo->tareas->where('estado', 'completada')->count(),
            ],
            
            // Comisión (solo si está habilitada)
            'comision' => $equipo->comision_habilitada ? [
                'habilitada' => true,
                'tipo' => $equipo->tipo_comision,
                'valor' => $equipo->valor_comision,
                'ganancia_estimada' => $ganancia,
            ] : [
                'habilitada' => false,
            ],
        ];
    }

    /**
     * Calcular ganancia por comisión
     */
    private function calcularGanancia($equipo, $totalTareas): float
    {
        if (!$equipo->comision_habilitada) {
            return 0;
        }

        if ($equipo->tipo_comision === 'porcentaje') {
            return $totalTareas * ($equipo->valor_comision / 100);
        } elseif ($equipo->tipo_comision === 'fijo') {
            return $equipo->valor_comision;
        }

        return 0;
    }
}