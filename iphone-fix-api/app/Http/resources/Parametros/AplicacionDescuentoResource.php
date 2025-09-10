<?php
// app/Http/Resources/Parametros/AplicacionDescuentoResource.php
namespace App\Http\Resources\Parametros;

use Illuminate\Http\Resources\Json\JsonResource;

class AplicacionDescuentoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'     => $this->id,
            'nombre' => $this->nombre,
            'activo' => (bool) $this->activo,
        ];
    }
}
