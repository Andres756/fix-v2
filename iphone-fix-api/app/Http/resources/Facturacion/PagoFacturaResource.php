<?php

namespace App\Http\Resources\Facturacion;

use Illuminate\Http\Resources\Json\JsonResource;

class PagoFacturaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'forma_pago'       => $this->formaPago?->nombre,
            'valor'            => $this->valor,
            'referencia_externa'=> $this->referencia_externa,
            'observaciones'    => $this->observaciones,
            'usuario'          => [
                'id'   => $this->usuario?->id,
                'name' => $this->usuario?->name,
            ],
            'fecha'            => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
