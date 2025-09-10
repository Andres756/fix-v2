<?php
// app/Http/Resources/Inventario/DetalleRepuestoResource.php
namespace App\Http\Resources\Inventario;

use Illuminate\Http\Resources\Json\JsonResource;

class DetalleRepuestoResource extends JsonResource
{
    public function toArray($req)
    {
        return [
            'modelo_compatible'     => $this->modelo_compatible,
            'tipo_repuesto'         => $this->tipo_repuesto,
            'referencia_fabricante' => $this->referencia_fabricante,
            'garantia_meses'        => $this->garantia_meses,
        ];
    }
}
