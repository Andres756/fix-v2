<?php

namespace App\Http\Controllers\Api\OrdenServicio;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrdenServicio\Equipo\CreateEquipoRequest;
use App\Http\Requests\OrdenServicio\Equipo\UpdateEquipoRequest;
use App\Http\Resources\OrdenServicio\EquipoOrdenServicioResource;
use App\Models\OrdenServicio\OrdenServicio;
use App\Models\OrdenServicio\EquipoOrdenServicio;

class EquipoOrdenServicioController extends Controller
{
    /**
     * Lista los equipos de una orden del cliente.
     */
    public function index($clienteId, $ordenId)
    {
        // Garantiza que la orden pertenece al cliente
        $orden = OrdenServicio::where('cliente_id', $clienteId)->findOrFail($ordenId);

        $equipos = $orden->equipos()->latest('id')->get();

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
            ->findOrFail($equipoId);

        return new EquipoOrdenServicioResource($equipo);
    }

    /**
     * Actualiza un equipo de la orden del cliente.
     */
    public function update(UpdateEquipoRequest $request, $clienteId, $ordenId, $equipoId)
    {
        $equipo = EquipoOrdenServicio::where('orden_id', $ordenId)
            ->whereHas('orden', function ($q) use ($clienteId) {
                $q->where('cliente_id', $clienteId);
            })
            ->findOrFail($equipoId);

        $data = $request->validated();

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

}
