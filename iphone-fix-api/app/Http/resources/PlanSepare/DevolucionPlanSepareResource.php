<?php

namespace App\Http\Resources\PlanSepare;

use Illuminate\Http\Resources\Json\JsonResource;

class DevolucionPlanSepareResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                    => $this->id,
            'plan_separe_id'        => $this->plan_separe_id,
            'valor_devolucion'      => $this->valor_devolucion,
            'porcentaje_devolucion' => $this->porcentaje_devolucion,
            'motivo'                => $this->motivo,
            'usuario_id'            => $this->usuario_id,
            'fecha_devolucion'      => $this->fecha_devolucion,
            'observaciones'         => $this->observaciones,
        ];
    }
}
