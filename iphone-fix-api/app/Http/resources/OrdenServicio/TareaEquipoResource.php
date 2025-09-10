<?php

namespace App\Http\Resources\OrdenServicio;

use Illuminate\Http\Resources\Json\JsonResource;

class TareaEquipoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'equipo_os_id'     => $this->equipo_os_id,
            'tipo_trabajo_id'  => $this->tipo_trabajo_id,
            'costo_aplicado'   => (float) $this->costo_aplicado,
            'estado'           => $this->estado,
            'observaciones'    => $this->observaciones,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,

            // Anidado (sólo si viene cargado con ->with('tipoTrabajo'))
            'tipo_trabajo' => $this->whenLoaded('tipoTrabajo', function () {
                return [
                    'id'             => $this->tipoTrabajo->id,
                    'nombre'         => $this->tipoTrabajo->nombre,
                    'costo_sugerido' => (float) $this->tipoTrabajo->costo_sugerido, // ✅ correcto
                ];
            }),

        ];
    }
}