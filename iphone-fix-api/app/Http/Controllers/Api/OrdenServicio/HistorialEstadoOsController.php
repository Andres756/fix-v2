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
        $orden = OrdenServicio::where('cliente_id', $clienteId)
            ->findOrFail($ordenId);

        // 🔹 Cargamos el historial con el usuario y ordenamos del más reciente al más antiguo
        $historial = HistorialEstadoOs::with('usuario')
            ->where('orden_id', $orden->id)
            ->orderByDesc('id')
            ->get();

        // 🔹 Enviamos la colección con el resource
        return HistorialEstadoOsResource::collection($historial);
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
