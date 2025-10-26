<?php

namespace App\Http\Resources\PlanSepare;

use Illuminate\Http\Resources\Json\JsonResource;

class LogPlanSepareResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'accion'       => $this->accion,
            'descripcion'  => $this->descripcion,
            'usuario'      => $this->usuario?->name,
            // 👇 se adapta automáticamente si es string o Carbon
            'fecha'        => $this->when(
                isset($this->created_at) || isset($this->fecha),
                $this->created_at instanceof \Carbon\Carbon
                    ? $this->created_at->format('Y-m-d H:i')
                    : (is_string($this->created_at ?? $this->fecha ?? null)
                        ? $this->created_at ?? $this->fecha
                        : null)
            ),
        ];
    }
}
