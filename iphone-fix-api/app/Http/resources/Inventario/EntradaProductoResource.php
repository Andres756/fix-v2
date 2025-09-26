<?php
// app/Http/Resources/Inventario/EntradaProductoResource.php

namespace App\Http\Resources\Inventario;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Parametros\MotivoIngresoResource;

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
            'costo_unitario'  => (string) $this->costo_unitario,
            'fecha_entrada'   => optional($this->fecha_entrada)->format('Y-m-d H:i:s'),
            'observaciones'   => $this->observaciones, // Renombrado desde notas
            
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
            
            // Relaciones
            'inventario'      => $this->whenLoaded('inventario', function() {
                return [
                    'id'     => $this->inventario->id,
                    'nombre' => $this->inventario->nombre,
                    'codigo' => $this->inventario->codigo,
                    'stock'  => $this->inventario->stock,
                    'costo'  => (string) $this->inventario->costo,
                ];
            }),
            
            'lote'            => $this->whenLoaded('lote', function() {
                return [
                    'id'          => $this->lote->id,
                    'numero_lote' => $this->lote->numero_lote,
                    'proveedor'   => $this->lote->proveedor 
                        ? ['id' => $this->lote->proveedor->id, 'nombre' => $this->lote->proveedor->nombre]
                        : null,
                ];
            }),
            
            'motivo'          => new MotivoIngresoResource($this->whenLoaded('motivo')),
        ];
    }
}