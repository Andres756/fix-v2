<?php

namespace App\Http\Resources\OrdenServicio;

use Illuminate\Http\Resources\Json\JsonResource;

class HistorialEstadoOsResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'orden_id' => $this->orden_id,
            'equipo_os_id' => $this->equipo_os_id,
            'tarea_id' => $this->tarea_id,
            'estado_anterior' => $this->estado_anterior,
            'estado_nuevo' => $this->estado_nuevo,
            'usuario_id' => $this->usuario_id,
            'comentario' => $this->comentario,
            'fecha_cambio' => $this->fecha_cambio,
        ];
    }
}
