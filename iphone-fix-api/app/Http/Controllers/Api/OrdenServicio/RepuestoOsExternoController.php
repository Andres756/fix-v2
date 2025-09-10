<?php

namespace App\Http\Controllers\Api\OrdenServicio;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrdenServicio\Repuesto\CreateRepuestoExternoRequest;
use App\Http\Requests\OrdenServicio\Repuesto\UpdateRepuestoExternoRequest;
use App\Http\Resources\OrdenServicio\RepuestoOsExternoResource;
use App\Models\OrdenServicio\EquipoOrdenServicio;
use App\Models\OrdenServicio\RepuestoOsExterno;

class RepuestoOsExternoController extends Controller
{
    public function index($clienteId, $ordenId, $equipoId)
    {
        $equipo = EquipoOrdenServicio::where('orden_id', $ordenId)
            ->whereHas('orden', fn($q) => $q->where('cliente_id', $clienteId))
            ->findOrFail($equipoId);

        return RepuestoOsExternoResource::collection($equipo->repuestosExternos);
    }

    public function store(CreateRepuestoExternoRequest $request, $clienteId, $ordenId, $equipoId)
    {
        $equipo = EquipoOrdenServicio::where('orden_id', $ordenId)
            ->whereHas('orden', fn($q) => $q->where('cliente_id', $clienteId))
            ->findOrFail($equipoId);

        $repuesto = $equipo->repuestosExternos()->create($request->validated());
        
        // Refrescar para obtener costo_total calculado
        $repuesto->refresh();

        return new RepuestoOsExternoResource($repuesto);
    }

    public function show($clienteId, $ordenId, $equipoId, $repuestoId)
    {
        $repuesto = RepuestoOsExterno::where('equipo_os_id', $equipoId)
            ->whereHas('equipo.orden', fn($q) => $q->where('id', $ordenId)->where('cliente_id', $clienteId))
            ->findOrFail($repuestoId);

        return new RepuestoOsExternoResource($repuesto);
    }

    public function update(UpdateRepuestoExternoRequest $request, $clienteId, $ordenId, $equipoId, $repuestoId)
    {
        $repuesto = RepuestoOsExterno::where('equipo_os_id', $equipoId)
            ->whereHas('equipo.orden', fn($q) => $q->where('id', $ordenId)->where('cliente_id', $clienteId))
            ->findOrFail($repuestoId);

        $repuesto->update($request->validated());
        
        // Refrescar para obtener costo_total recalculado
        $repuesto->refresh();

        return new RepuestoOsExternoResource($repuesto);
    }

    public function destroy($clienteId, $ordenId, $equipoId, $repuestoId)
    {
        $repuesto = RepuestoOsExterno::where('equipo_os_id', $equipoId)
            ->whereHas('equipo.orden', fn($q) => $q->where('id', $ordenId)->where('cliente_id', $clienteId))
            ->findOrFail($repuestoId);

        $repuesto->delete();

        return response()->json(['message' => 'Repuesto externo eliminado'], 200);
    }
}