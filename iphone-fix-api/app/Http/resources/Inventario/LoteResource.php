<?php
// app/Http/Resources/Inventario/LoteResource.php
namespace App\Http\Resources\Inventario;

use Illuminate\Http\Resources\Json\JsonResource;

class LoteResource extends JsonResource
{
    public function toArray($req)
    {
        return [
            'id'           => $this->id,
            'codigo_lote'  => $this->codigo_lote,
            'proveedor_id' => $this->proveedor_id,
            'costo_envio'  => (string) $this->costo_envio,
            'fecha_ingreso'=> optional($this->fecha_ingreso)->toDateString(),
            'notas'        => $this->notas,
            'proveedor'    => new ProveedorResource($this->whenLoaded('proveedor')),
        ];
    }
}
