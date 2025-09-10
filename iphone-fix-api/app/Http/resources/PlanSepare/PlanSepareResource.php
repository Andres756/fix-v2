<?php
// app/Http/Resources/PlanSepare/PlanSepareResource.php
namespace App\Http\Resources\PlanSepare;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanSepareResource extends JsonResource
{
    public function toArray($req)
    {
        return [
            'id'                  => $this->id,
            'cliente_id'          => $this->cliente_id,
            'inventario_id'       => $this->inventario_id,
            'inventario_id_asignado' => $this->inventario_id_asignado,
            'cambio_equipo'       => (bool) $this->cambio_equipo,
            'precio_total'        => (string) $this->precio_total,
            'porcentaje_minimo'   => (string) $this->porcentaje_minimo,
            'abono_inicial'       => (string) $this->abono_inicial,
            'monto_devuelto'      => (string) $this->monto_devuelto,
            'estado_id'           => $this->estado_id,
            'observaciones'       => $this->observaciones,
            'created_at'          => optional($this->created_at)->toDateTimeString(),
            'updated_at'          => optional($this->updated_at)->toDateTimeString(),
            'abonos'              => AbonoPlanSepareResource::collection($this->whenLoaded('abonos')),
            'logs'                => PlanSepareLogResource::collection($this->whenLoaded('logs')),
        ];
    }
}
