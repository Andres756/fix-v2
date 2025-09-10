<?php
// app/Http/Resources/Ventas/FacturaResource.php
namespace App\Http\Resources\Ventas;

use Illuminate\Http\Resources\Json\JsonResource;

class FacturaResource extends JsonResource
{
    public function toArray($req)
    {
        return [
            'id'           => $this->id,
            'cliente_id'   => $this->cliente_id,
            'usuario_id'   => $this->usuario_id,
            'tipo_venta_id'=> $this->tipo_venta_id,
            'estado_id'    => $this->estado_id,
            'forma_pago_id'=> $this->forma_pago_id,
            'total'        => (string) $this->total,
            'observaciones'=> $this->observaciones,
            'created_at'   => optional($this->created_at)->toDateTimeString(),
            'updated_at'   => optional($this->updated_at)->toDateTimeString(),
            'detalles'     => DetalleFacturaResource::collection($this->whenLoaded('detalles')),
            'logs'         => FacturaLogResource::collection($this->whenLoaded('logs')),
        ];
    }
}
