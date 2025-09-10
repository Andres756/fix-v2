<?php
// app/Http/Resources/PlanSepare/AbonoPlanSepareResource.php
namespace App\Http\Resources\PlanSepare;

use Illuminate\Http\Resources\Json\JsonResource;

class AbonoPlanSepareResource extends JsonResource
{
    public function toArray($req)
    {
        return [
            'id'           => $this->id,
            'plan_id'      => $this->plan_id,
            'monto'        => (string) $this->monto,
            'forma_pago_id'=> $this->forma_pago_id,
            'usuario_id'   => $this->usuario_id,
            'fecha_abono'  => optional($this->fecha_abono)->toDateString(),
        ];
    }
}
