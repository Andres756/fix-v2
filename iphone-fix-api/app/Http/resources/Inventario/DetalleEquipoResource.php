<?php
// app/Http/Resources/Inventario/DetalleEquipoResource.php
namespace App\Http\Resources\Inventario;

use Illuminate\Http\Resources\Json\JsonResource;

class DetalleEquipoResource extends JsonResource
{
    public function toArray($req)
    {
        return [
            'imei_1'        => $this->imei_1,
            'imei_2'        => $this->imei_2,
            'estado_fisico' => $this->estado_fisico,
            'version_ios'   => $this->version_ios,
            'almacenamiento'=> $this->almacenamiento,
            'color'         => $this->color,
            'modelo'        => new ModeloEquipoResource($this->whenLoaded('modeloEquipo')), // Incluye la relación modeloEquipo
        ];
    }
}
