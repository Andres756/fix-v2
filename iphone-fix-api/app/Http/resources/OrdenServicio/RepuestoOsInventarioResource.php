<?php

namespace App\Http\Resources\OrdenServicio;

use Illuminate\Http\Resources\Json\JsonResource;

class RepuestoOsInventarioResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                     => $this->id,
            'equipo_os_id'           => $this->equipo_os_id,
            'inventario_id'          => $this->inventario_id,
            'cantidad'               => $this->cantidad,
            'costo_unitario_aplicado'=> number_format($this->costo_unitario_aplicado, 2, '.', ''),
            'costo_total'            => number_format($this->costo_total, 2, '.', ''),
            'observaciones'          => $this->observaciones,
            'fecha_uso'              => $this->fecha_uso,
            'created_at'             => $this->created_at,
            'updated_at'             => $this->updated_at,

            // 👇 Simplificamos para que tu tabla funcione directo con r.codigo y r.nombre
            'codigo'                 => $this->inventario?->codigo_producto ?? $this->inventario?->codigo,
            'nombre'                 => $this->inventario?->nombre,

            // Información completa (por si necesitas más en otros lados)
            'inventario' => $this->whenLoaded('inventario', function () {
                return [
                    'id'                => $this->inventario->id,
                    'nombre'            => $this->inventario->nombre,
                    'descripcion'       => $this->inventario->descripcion,
                    'precio'            => number_format($this->inventario->precio, 2, '.', ''),
                    'stock'             => $this->inventario->stock,
                    'codigo_producto'   => $this->inventario->codigo_producto,
                    'tipo_inventario_id'=> $this->inventario->tipo_inventario_id,
                    'unidad_medida'     => $this->inventario->unidad_medida ?? null,
                    'marca'             => $this->inventario->marca ?? null,
                    'modelo'            => $this->inventario->modelo ?? null,
                ];
            }),

            'puede_editar_cantidad' => $this->whenLoaded('inventario', fn () => $this->inventario->stock > 0),
            'stock_disponible'      => $this->whenLoaded('inventario', fn () => $this->inventario->stock),

            'precio_cambio' => $this->whenLoaded('inventario', function () {
                $precioActual   = $this->inventario->precio;
                $precioAplicado = $this->costo_unitario_aplicado;

                return [
                    'precio_al_usar' => number_format($precioAplicado, 2, '.', ''),
                    'precio_actual'  => number_format($precioActual, 2, '.', ''),
                    'hay_cambio'     => $precioActual != $precioAplicado,
                ];
            }),
        ];
    }
}