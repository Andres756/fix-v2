<?php
// app/Http/Resources/Parametros/MotivoIngresoResource.php
namespace App\Http\Resources\Parametros;

use Illuminate\Http\Resources\Json\JsonResource;

class MotivoIngresoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'     => $this->id,
            'nombre' => $this->nombre,
        ];
    }
}
