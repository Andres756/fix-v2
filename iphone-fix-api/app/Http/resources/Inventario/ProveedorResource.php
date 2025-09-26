<?php
// app/Http/Resources/Inventario/ProveedorResource.php

namespace App\Http\Resources\Inventario;

use Illuminate\Http\Resources\Json\JsonResource;

class ProveedorResource extends JsonResource
{
    public function toArray($req)
    {
        return [
            'id'              => $this->id,
            'nombre'          => $this->nombre,
            'contacto_nombre' => $this->contacto_nombre,
            'telefono'        => $this->telefono,
            'correo'          => $this->correo,
            'direccion'       => $this->direccion,
            
            'creado_en'       => $this->creado_en,
            'actualizado_en'  => $this->actualizado_en,
        ];
    }
}