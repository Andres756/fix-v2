<?php
// app/Http/Resources/Parametros/EstadoOrdenServicioResource.php
namespace App\Http\Resources\Parametros;

use Illuminate\Http\Resources\Json\JsonResource;

class EstadoOrdenServicioResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'     => $this->id,
            'nombre' => $this->nombre,
        ];
    }
}
