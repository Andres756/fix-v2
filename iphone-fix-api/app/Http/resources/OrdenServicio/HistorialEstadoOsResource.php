<?php

namespace App\Http\Resources\OrdenServicio;

use Illuminate\Http\Resources\Json\JsonResource;

class HistorialEstadoOsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'orden_id' => $this->orden_id,
            'usuario' => $this->usuario?->name ?? '—',
            'estado_anterior' => $this->estado_anterior ?? '—',
            'estado_nuevo' => $this->estado_nuevo ?? '—',
            'descripcion' => $this->descripcion ?? '—',
            'fecha' => $this->created_at
                ? $this->created_at->format('Y-m-d H:i:s')
                : null,
        ];
    }
}
