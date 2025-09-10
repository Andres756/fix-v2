<?php

namespace App\Http\Controllers\Api\OrdenServicio;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrdenServicio\HistorialEstadoOsResource;
use App\Models\OrdenServicio\OrdenServicio;
use App\Models\OrdenServicio\EquipoOrdenServicio;
use App\Models\OrdenServicio\HistorialEstadoOs;

class HistorialEstadoOsController extends Controller
{
    public function ordenHistorial($clienteId, $ordenId)
    {
        $orden = OrdenServicio::where('cliente_id', $clienteId)->findOrFail($ordenId);
        return HistorialEstadoOsResource::collection(
            HistorialEstadoOs::where('orden_id', $orden->id)->get()
        );
    }

    public function equipoHistorial($clienteId, $ordenId, $equipoId)
    {
        $equipo = EquipoOrdenServicio::where('orden_id', $ordenId)
            ->whereHas('orden', fn($q) => $q->where('cliente_id', $clienteId))
            ->findOrFail($equipoId);

        return HistorialEstadoOsResource::collection(
            HistorialEstadoOs::where('equipo_os_id', $equipo->id)->get()
        );
    }
}
