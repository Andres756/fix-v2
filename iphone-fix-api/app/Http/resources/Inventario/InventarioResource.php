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
            'categoria_id'   => $this->categoria_id,
            'lote_id'        => $this->lote_id,
            'estado_id'      => $this->estado_inventario_id,
            'tipo_id'        => $this->tipo_inventario_id,
            'proveedor_id'   => $this->proveedor_id,
            'stock'          => $this->stock,
            'stock_min'      => $this->stock_minimo,
            'precio'         => (string) $this->precio,
            'costo'          => (string) $this->costo,
            'costo_mayor'    => (string) $this->costo_mayor,
            'tipo_impuesto'  => $this->tipo_impuesto,
            'valor_impuesto' => (string) $this->valor_impuesto,
            'precio_final'   => (string) $this->precio_final, // campo generado
            'activo'         => (bool) $this->activo,
            'fecha_ingreso'  => optional($this->fecha_ingreso)->toDateString(),
            'notas'          => $this->notas,

            'ruta_imagen'    => $this->ruta_imagen,
            'imagen_url'     => $this->imagen_url, // 👈 URL ABSOLUTA proveniente del accessor del modelo

            'categoria'        => new CategoriaResource($this->whenLoaded('categoria')),
            'proveedor'        => new ProveedorResource($this->whenLoaded('proveedor')),
            'lote'             => new LoteResource($this->whenLoaded('lote')),
            'detalle_equipo'   => new DetalleEquipoResource($this->whenLoaded('detalleEquipo')),
            'detalle_producto' => new DetalleProductoResource($this->whenLoaded('detalleProducto')),
            'detalle_repuesto' => new DetalleRepuestoResource($this->whenLoaded('detalleRepuesto')),
        ];
    }
}
