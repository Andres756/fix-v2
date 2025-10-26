<?php

namespace App\Http\Resources\PlanSepare;

use Illuminate\Http\Resources\Json\JsonResource;

class AbonoPlanSepareResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'valor'         => $this->valor,
            'forma_pago'    => $this->formaPago ? $this->formaPago->nombre : null,
            'observaciones' => $this->observaciones,
            'usuario'       => $this->usuario?->name,
            'fecha'         => $this->created_at?->format('Y-m-d H:i')
        ];
    }
}
