<?php

namespace App\Http\Resources\Facturacion;

use Illuminate\Http\Resources\Json\JsonResource;

class FacturaDetalleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'tipo_item'      => $this->tipo_item,
            'referencia_id'  => $this->referencia_id,
            'descripcion'    => $this->descripcion,
            'cantidad'       => $this->cantidad,
            'valor_unitario' => $this->valor_unitario,
            'descuento'      => $this->descuento,
            'impuesto'       => $this->impuesto,
            'total'          => $this->total,
        ];
    }
}
