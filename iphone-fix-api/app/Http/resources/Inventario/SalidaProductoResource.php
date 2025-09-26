<?php
// app/Http/Resources/Inventario/SalidaProductoResource.php

namespace App\Http\Resources\Inventario;

use Illuminate\Http\Resources\Json\JsonResource;

class SalidaProductoResource extends JsonResource
{
    public function toArray($req)
    {
        return [
            'id'              => $this->id,
            'inventario_id'   => $this->inventario_id,
            'tipo_salida'     => $this->tipo_salida,
            'cantidad'        => $this->cantidad,
            'costo_unitario'  => (string) $this->costo_unitario,
            'referencia_id'   => $this->referencia_id,
            'fecha_salida'    => optional($this->fecha_salida)->format('Y-m-d H:i:s'),
            'observaciones'   => $this->observaciones,
            
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
            
            // Relación
            'inventario'      => $this->whenLoaded('inventario', function() {
                return [
                    'id'     => $this->inventario->id,
                    'nombre' => $this->inventario->nombre,
                    'codigo' => $this->inventario->codigo,
                    'stock'  => $this->inventario->stock,
                ];
            }),
            
            // Información adicional según tipo
            'tipo_salida_label' => $this->getTipoSalidaLabel(),
        ];
    }
    
    private function getTipoSalidaLabel(): string
    {
        return match($this->tipo_salida) {
            'venta' => 'Venta',
            'orden_servicio' => 'Orden de Servicio',
            'ajuste' => 'Ajuste de Inventario',
            'perdida' => 'Pérdida/Daño',
            default => 'Desconocido'
        };
    }
}