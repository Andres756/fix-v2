<?php

namespace App\Http\Resources\OrdenServicio;

use Illuminate\Http\Resources\Json\JsonResource;

class RepuestoOsExternoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'equipo_os_id' => $this->equipo_os_id,
            'descripcion' => $this->descripcion,
            'cantidad' => $this->cantidad,
            'costo_unitario' => number_format($this->costo_unitario, 2, '.', ''),
            'costo_total' => number_format($this->costo_total, 2, '.', ''),
            'proveedor_id' => $this->proveedor_id,
            'proveedor' => $this->whenLoaded('proveedor', function () {
                return [
                    'id' => $this->proveedor->id,
                    'nombre' => $this->proveedor->nombre,
                    'contacto_nombre' => $this->proveedor->contacto_nombre,
                    'telefono' => $this->proveedor->telefono,
                    'correo' => $this->proveedor->correo,
                ];
            }),
            'observaciones' => $this->observaciones,
            'fecha_gasto' => $this->fecha_gasto,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}