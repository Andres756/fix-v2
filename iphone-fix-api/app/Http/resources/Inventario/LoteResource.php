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
            'numero_lote'  => $this->numero_lote,        // Renombrado desde codigo_lote
            'proveedor_id' => $this->proveedor_id,
            'costo_flete'  => (string) $this->costo_flete, // Renombrado desde costo_envio
            'fecha_ingreso'=> optional($this->fecha_ingreso)->toDateString(),
            'notas'        => $this->notas,
            
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
            
            // Relación
            'proveedor'    => new ProveedorResource($this->whenLoaded('proveedor')),
            
            // Entradas asociadas (opcional)
            'entradas'     => EntradaProductoResource::collection($this->whenLoaded('entradas')),
        ];
    }
}