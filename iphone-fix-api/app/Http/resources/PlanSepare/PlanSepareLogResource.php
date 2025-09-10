<?php
// app/Http/Resources/PlanSepare/PlanSepareLogResource.php
namespace App\Http\Resources\PlanSepare;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanSepareLogResource extends JsonResource
{
    public function toArray($req)
    {
        return [
            'id'                  => $this->id,
            'plan_id'             => $this->plan_id,
            'tipo_cambio_id'      => $this->tipo_cambio_id,
            'inventario_anterior_id' => $this->inventario_anterior_id,
            'inventario_nuevo_id' => $this->inventario_nuevo_id,
            'precio_anterior'     => (string) $this->precio_anterior,
            'precio_nuevo'        => (string) $this->precio_nuevo,
            'monto_devuelto'      => (string) $this->monto_devuelto,
            'observaciones'       => $this->observaciones,
            'usuario_id'          => $this->usuario_id,
            'fecha'               => optional($this->fecha)->toDateTimeString(),
        ];
    }
}
