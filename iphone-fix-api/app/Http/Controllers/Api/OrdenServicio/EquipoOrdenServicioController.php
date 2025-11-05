<?php

namespace App\Http\Controllers\Api\OrdenServicio;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrdenServicio\Equipo\CreateEquipoRequest;
use App\Http\Requests\OrdenServicio\Equipo\UpdateEquipoRequest;
use App\Http\Resources\OrdenServicio\EquipoOrdenServicioResource;
use App\Models\OrdenServicio\OrdenServicio;
use App\Models\OrdenServicio\EquipoOrdenServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class EquipoOrdenServicioController extends Controller
{
    /**
     * Lista los equipos de una orden del cliente con sus totales.
     */
    public function index($clienteId, $ordenId)
    {
        // Validar que la orden pertenezca al cliente
        $orden = OrdenServicio::where('cliente_id', $clienteId)->findOrFail($ordenId);

        // Cargar relaciones necesarias
        $equipos = $orden->equipos()
            ->with(['tareas', 'repuestosInventario', 'repuestosExternos'])
            ->latest('id')
            ->get();

        // Calcular costos totales reutilizando la lógica del método costos()
        $equipos->transform(function ($equipo) {
            $costoActividades = $equipo->tareas->sum('costo_aplicado');
            $costoRepuestos   = $equipo->repuestosInventario->sum('costo_total');
            $costoExternos    = $equipo->repuestosExternos->sum('costo_total');

            $costoReal = $costoActividades + $costoRepuestos + $costoExternos;

            $valorEstimado = $equipo->valor_estimado ?? 0;
            $diferencia    = $costoReal - $valorEstimado;

            // Agregar los campos calculados directamente al modelo
            $equipo->precio_total = $costoReal;
            $equipo->costo_actividades = $costoActividades;
            $equipo->costo_repuestos   = $costoRepuestos;
            $equipo->costo_externos    = $costoExternos;
            $equipo->diferencia        = $diferencia;
            $equipo->estado_presupuesto = $diferencia > 0
                ? 'superado'
                : ($diferencia < 0 ? 'por_debajo' : 'exacto');

            return $equipo;
        });

        // Devolver usando el Resource para mantener consistencia
        return EquipoOrdenServicioResource::collection($equipos);
    }

    /**
     * Crea un equipo para la orden indicada.
     */
    public function store(CreateEquipoRequest $request, $clienteId, $ordenId)
    {
        $orden = OrdenServicio::where('cliente_id', $clienteId)->findOrFail($ordenId);

        $data = $request->validated();

        // Normaliza campos de comisión cuando no está habilitada
        if (empty($data['comision_habilitada'])) {
            $data['tipo_comision']  = null;
            $data['valor_comision'] = null;
        }

        $equipo = $orden->equipos()->create($data);

        return new EquipoOrdenServicioResource($equipo);
    }

    /**
     * Muestra un equipo específico de la orden del cliente.
     */
    public function show($clienteId, $ordenId, $equipoId)
    {
        $equipo = EquipoOrdenServicio::where('orden_id', $ordenId)
            ->whereHas('orden', function ($q) use ($clienteId) {
                $q->where('cliente_id', $clienteId);
            })
            ->with(['tareas', 'repuestosInventario', 'repuestosExternos'])
            ->findOrFail($equipoId);

        // Calcular el total como en index()
        $costoActividades = $equipo->tareas->sum('costo_aplicado');
        $costoRepuestos   = $equipo->repuestosInventario->sum('costo_total');
        $costoExternos    = $equipo->repuestosExternos->sum('costo_total');
        $costoReal        = $costoActividades + $costoRepuestos + $costoExternos;

        $equipo->precio_total = $costoReal;

        return new EquipoOrdenServicioResource($equipo);
    }

    /**
     * Actualiza un equipo de la orden del cliente.
     */
    public function update(UpdateEquipoRequest $request, $clienteId, $ordenId, $equipoId)
    {
        $equipo = EquipoOrdenServicio::where('orden_id', $ordenId)
            ->whereHas('orden', fn($q) => $q->where('cliente_id', $clienteId))
            ->with('tareas')
            ->findOrFail($equipoId);

        $data = $request->validated();

        // 🔸 Si intenta marcar como "finalizado", validamos tareas
        if (isset($data['estado']) && strtolower($data['estado']) === 'finalizado') {
            $totalTareas = $equipo->tareas->count();
            $tareasCompletas = $equipo->tareas->where('estado', 'completada')->count();

            if ($totalTareas > 0 && $tareasCompletas < $totalTareas) {
                return response()->json([
                    'message' => 'No se puede finalizar el equipo porque tiene tareas pendientes.',
                    'tareas_pendientes' => $totalTareas - $tareasCompletas,
                ], 422);
            }

            // ✅ Si no hay tareas o todas completas, marcamos finalizado
            $data['fecha_finalizacion'] = now();
        }

        if (empty($data['comision_habilitada'])) {
            $data['tipo_comision']  = null;
            $data['valor_comision'] = null;
        }

        $equipo->update($data);

        return new EquipoOrdenServicioResource($equipo->fresh());
    }

    /**
     * Elimina un equipo de la orden del cliente.
     */
    public function destroy($clienteId, $ordenId, $equipoId)
    {
        $equipo = EquipoOrdenServicio::where('orden_id', $ordenId)
            ->whereHas('orden', fn($q) => $q->where('cliente_id', $clienteId))
            ->findOrFail($equipoId);

        $equipo->delete();

        return response()->json(['message' => 'Equipo eliminado'], 200);
    }
    /**
     * Obtiene los costos totales de un equipo.
     */
    public function costos($equipoId)
    {
        $equipo = EquipoOrdenServicio::with(['tareas', 'repuestosInventario', 'repuestosExternos'])
            ->findOrFail($equipoId);

        $costoActividades = $equipo->tareas->sum('costo_aplicado');
        $costoRepuestos   = $equipo->repuestosInventario->sum('costo_total');
        $costoExternos    = $equipo->repuestosExternos->sum('costo_total');

        $costoReal = $costoActividades + $costoRepuestos + $costoExternos;

        $valorEstimado = $equipo->valor_estimado ?? 0;
        $diferencia    = $costoReal - $valorEstimado;

        return response()->json([
            'equipo_id'          => $equipo->id,
            'valor_estimado'     => $valorEstimado,
            'costo_actividades'  => $costoActividades,
            'costo_repuestos'    => $costoRepuestos,
            'costo_externos'     => $costoExternos,
            'costo_real'         => $costoReal,
            'diferencia'         => $diferencia,
            'estado_presupuesto' => $diferencia > 0 
                                    ? 'superado' 
                                    : ($diferencia < 0 ? 'por_debajo' : 'exacto'),
        ]);
    }

    public function actualizarEstado(Request $request, $equipoId)
    {
        $request->validate([
            'estado' => 'required|string',
        ]);

        $equipo = \App\Models\OrdenServicio\EquipoOrdenServicio::findOrFail($equipoId);

        // ✅ Validar que todas las tareas estén completadas si se marca como finalizado
        if ($request->estado === 'finalizado') {
            $tareasIncompletas = $equipo->tareas()->where('estado', '!=', 'completada')->count();
            if ($tareasIncompletas > 0) {
                return response()->json([
                    'message' => 'No se puede finalizar el equipo: aún hay tareas pendientes.'
                ], 422);
            }
        }

        $equipo->estado = $request->estado;
        $equipo->save();

        Log::info('✅ Estado actualizado', [
            'equipo_id' => $equipo->id,
            'nuevo_estado' => $equipo->estado
        ]);

        return response()->json([
            'message' => 'Estado actualizado correctamente.',
            'equipo' => $equipo
        ]);
    }

}
