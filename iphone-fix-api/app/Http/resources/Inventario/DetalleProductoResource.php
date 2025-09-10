<?php
// app/Http/Resources/Inventario/DetalleProductoResource.php
namespace App\Http\Resources\Inventario;

use Illuminate\Http\Resources\Json\JsonResource;

class DetalleProductoResource extends JsonResource
{
    public function toArray($req)
    {
        return [
            'material'       => $this->material,
            'compatibilidad' => $this->compatibilidad,
            'tipo_accesorio' => $this->tipo_accesorio,
        ];
    }
}
