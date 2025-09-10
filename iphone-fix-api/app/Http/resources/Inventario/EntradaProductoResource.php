<?php
// app/Http/Resources/Inventario/EntradaProductoResource.php
namespace App\Http\Resources\Inventario;

use Illuminate\Http\Resources\Json\JsonResource;

class EntradaProductoResource extends JsonResource
{
    public function toArray($req)
    {
        return [
            'id'              => $this->id,
            'inventario_id'   => $this->inventario_id,
            'lote_id'         => $this->lote_id,
            'motivo_ingreso_id'=> $this->motivo_ingreso_id,
            'cantidad'        => $this->cantidad,
            'fecha_entrada'   => optional($this->fecha_entrada)->toDateString(),
            'notas'           => $this->notas,
            'inventario'      => new InventarioResource($this->whenLoaded('inventario')),
            'lote'            => new LoteResource($this->whenLoaded('lote')),
        ];
    }
}
