<?php

namespace App\Http\Resources\OrdenServicio;

use Illuminate\Http\Resources\Json\JsonResource;

class OrdenServicioResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                     => $this->id,
            'codigo_orden'           => $this->codigo_orden,
            'cliente_id'             => $this->cliente_id,

            // 👇 Se incluirá solo si hiciste ->with('cliente') en la consulta
            'cliente' => $this->whenLoaded('cliente', function () {
                return [
                    'id'        => $this->cliente->id,
                    'nombre'    => $this->cliente->nombre,
                    'documento' => $this->cliente->documento,
                ];
            }),

            'estado'                 => $this->estado,
            'observaciones_generales'=> $this->observaciones_generales,
            'fecha_creacion'         => $this->fecha_creacion,
            'fecha_cierre'           => $this->fecha_cierre,

            // 👇 Se incluirá solo si hiciste ->with('equipos')
            'equipos' => EquipoOrdenServicioResource::collection(
                $this->whenLoaded('equipos')
            ),

            'created_at'             => $this->created_at,
            'updated_at'             => $this->updated_at,
        ];
    }
}
