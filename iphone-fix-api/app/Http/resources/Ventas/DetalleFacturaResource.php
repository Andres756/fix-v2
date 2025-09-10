<?php
// app/Http/Resources/Ventas/DetalleFacturaResource.php
namespace App\Http\Resources\Ventas;

use Illuminate\Http\Resources\Json\JsonResource;

class DetalleFacturaResource extends JsonResource
{
    public function toArray($req)
    {
        return [
            'id'             => $this->id,
            'factura_id'     => $this->factura_id,
            'tipo_item_id'   => $this->tipo_item_id,
            'referencia_id'  => $this->referencia_id,
            'descripcion'    => $this->descripcion,
            'cantidad'       => $this->cantidad,
            'precio_unitario'=> (string) $this->precio_unitario,
            'descuento'      => (string) $this->descuento,
            'impuesto'       => (string) $this->impuesto,
            'total'          => (string) $this->total,
        ];
    }
}
