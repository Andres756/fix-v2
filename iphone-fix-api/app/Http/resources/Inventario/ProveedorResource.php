<?php
// app/Http/Resources/Inventario/ProveedorResource.php
namespace App\Http\Resources\Inventario;

use Illuminate\Http\Resources\Json\JsonResource;

class ProveedorResource extends JsonResource
{
    public function toArray($req)
    {
        return [
            'id'       => $this->id,
            'nombre'   => $this->nombre,
            'contacto' => $this->contacto_nombre,
            'telefono' => $this->telefono,
            'correo'   => $this->correo,
            'direccion'=> $this->direccion,
            'creado_en'=> optional($this->creado_en)->toDateTimeString(),
            'actualizado_en'=> optional($this->actualizado_en)->toDateTimeString(),
        ];
    }
}
