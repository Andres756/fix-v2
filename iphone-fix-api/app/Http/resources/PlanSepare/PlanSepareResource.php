<?php

namespace App\Http\Resources\PlanSepare;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanSepareResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'cliente'           => $this->cliente ? [
                'id'     => $this->cliente->id,
                'nombre' => $this->cliente->nombre
            ] : null,
            'inventario'        => $this->inventario ? [
                'id'     => $this->inventario->id,
                'nombre' => $this->inventario->nombre,
                'precio' => $this->inventario->precio
            ] : null,
            'precio_total'      => $this->precio_total,
            'porcentaje_minimo' => $this->porcentaje_minimo,
            'abono_inicial'     => $this->abono_inicial,
            'total_abonos'      => $this->total_abonos,
            'estado'            => $this->estado ? $this->estado->nombre : null,
            'codigo_estado'     => $this->estado ? $this->estado->codigo : null,
            'observaciones'     => $this->observaciones,
            'created_at'        => $this->created_at?->format('Y-m-d H:i'),

            // ✅ Abonos y Logs (ya los tienes)
            'abonos'            => AbonoPlanSepareResource::collection($this->whenLoaded('abonos')),
            'logs'              => LogPlanSepareResource::collection($this->whenLoaded('logs')),

            // ✅ Nueva sección: devoluciones
            'devoluciones' => $this->whenLoaded('devoluciones', function () {
                return $this->devoluciones->map(function ($dev) {
                    return [
                        'id'                   => $dev->id,
                        'valor_devolucion'     => $dev->valor_devolucion,
                        'porcentaje_devolucion'=> $dev->porcentaje_devolucion,
                        'motivo'               => $dev->motivo,
                        'usuario_id'           => $dev->usuario_id,
                        'fecha_devolucion'     => $dev->fecha_devolucion,
                        'observaciones'        => $dev->observaciones,
                    ];
                });
            }),
        ];
    }
}
