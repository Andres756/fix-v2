<?php

namespace App\Http\Resources\Inventario;

use Illuminate\Http\Resources\Json\JsonResource;

class InventarioExportResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            $this->id,
            $this->nombre,
            $this->codigo,
            $this->categoria->nombre ?? '',
            $this->stock,
            $this->stock_minimo,
            $this->costo,
            $this->precio,
            $this->tipo_impuesto,
            $this->activo ? 'Sí' : 'No',
            $this->created_at->format('Y-m-d'),
        ];
    }
}
