<?php

namespace App\Http\Resources\OrdenServicio;

use Illuminate\Http\Resources\Json\JsonResource;

class EquipoOrdenServicioResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                     => $this->id,
            'orden_id'               => $this->orden_id,

            'imei_serial'            => $this->imei_serial,
            'marca'                  => $this->marca,
            'modelo'                 => $this->modelo,
            'descripcion_problema'   => $this->descripcion_problema,
            'contrasena_equipo'      => $this->contrasena_equipo,
            'valor_estimado'         => $this->valor_estimado !== null ? (float)$this->valor_estimado : null,
            'fecha_estimada_entrega' => optional($this->fecha_estimada_entrega)->toDateString(),

            'tecnico_asignado'       => $this->tecnico_asignado,
            'tecnico'                => $this->whenLoaded('tecnico', function () {
                return [
                    'id'     => $this->tecnico->id,
                    'nombre' => $this->tecnico->name ?? $this->tecnico->nombre ?? null,
                    'email'  => $this->tecnico->email ?? null,
                ];
            }),

            'comision_habilitada'    => (bool)$this->comision_habilitada,
            'tipo_comision'          => $this->tipo_comision,
            'valor_comision'         => $this->valor_comision !== null ? (float)$this->valor_comision : null,

            'estado'                 => $this->estado,
            'observaciones'          => $this->observaciones,
            'fecha_finalizacion'     => $this->fecha_finalizacion ? $this->fecha_finalizacion->toDateTimeString() : null,

            'created_at'             => optional($this->created_at)->toDateTimeString(),
            'updated_at'             => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
