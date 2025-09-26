<?php
// app/Http/Resources/Inventario/InventarioResource.php

namespace App\Http\Resources\Inventario;

use Illuminate\Http\Resources\Json\JsonResource;

class InventarioResource extends JsonResource
{
    public function toArray($req)
    {
        return [
            'id'             => $this->id,
            'nombre'         => $this->nombre,
            'nombre_full'    => $this->nombre_detallado,
            'codigo'         => $this->codigo,
            
            // IDs
            'categoria_id'   => $this->categoria_id,
            'estado_id'      => $this->estado_inventario_id,
            'tipo_id'        => $this->tipo_inventario_id,
            
            // ELIMINADOS: lote_id, proveedor_id
            
            // Stock y precios
            'stock'          => $this->stock,
            'stock_min'      => $this->stock_minimo,
            'precio'         => (string) $this->precio,
            'costo'          => (string) $this->costo,
            'costo_mayor'    => (string) $this->costo_mayor,
            
            // Impuestos
            'tipo_impuesto'  => $this->tipo_impuesto,
            'valor_impuesto' => (string) $this->valor_impuesto,
            
            // Otros
            'notas'          => $this->notas,
            'ruta_imagen'    => $this->ruta_imagen,
            'imagen_url'     => $this->imagen_url, // Accessor del modelo
            
            'updated_at'     => $this->updated_at,
            'created_at'     => $this->created_at,
            
            // Relaciones
            'categoria'        => new CategoriaResource($this->whenLoaded('categoria')),
            'estado'           => $this->whenLoaded('estado', function() {
                return ['id' => $this->estado->id, 'nombre' => $this->estado->nombre];
            }),
            'tipo'             => $this->whenLoaded('tipo', function() {
                return ['id' => $this->tipo->id, 'nombre' => $this->tipo->nombre];
            }),
            
            // Detalles por tipo
            'detalle_equipo'   => new DetalleEquipoResource($this->whenLoaded('detalleEquipo')),
            'detalle_producto' => new DetalleProductoResource($this->whenLoaded('detalleProducto')),
            'detalle_repuesto' => new DetalleRepuestoResource($this->whenLoaded('detalleRepuesto')),
            
            // Movimientos (opcional)
            'entradas'         => EntradaProductoResource::collection($this->whenLoaded('entradas')),
            'salidas'          => SalidaProductoResource::collection($this->whenLoaded('salidas')),
        ];
    }
}