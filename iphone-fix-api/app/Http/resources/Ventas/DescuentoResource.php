<?php
// app/Http/Resources/Ventas/DescuentoResource.php
namespace App\Http\Resources\Ventas;

use Illuminate\Http\Resources\Json\JsonResource;

class DescuentoResource extends JsonResource
{
    public function toArray($req)
    {
        return [
            'id'        => $this->id,
            'nombre'    => $this->nombre,
            'tipo_id'   => $this->tipo_descuento_id,
            'aplica_a'  => $this->aplica_a_id,
            'valor'     => (string) $this->valor,
            'activo'    => (bool) $this->activo,
        ];
    }
}
