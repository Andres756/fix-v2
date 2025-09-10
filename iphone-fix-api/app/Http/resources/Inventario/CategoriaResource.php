<?php
// app/Http/Resources/Inventario/CategoriaResource.php
namespace App\Http\Resources\Inventario;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoriaResource extends JsonResource
{
    public function toArray($req)
    {
        return [
            'id'          => $this->id,
            'nombre'      => $this->nombre,
            'descripcion' => $this->descripcion,
            'tipo_id'     => $this->tipo_inventario_id,
        ];
    }
}
