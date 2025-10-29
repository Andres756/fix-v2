<?php

namespace App\Http\Resources\Facturacion;

use Illuminate\Http\Resources\Json\JsonResource;

class FacturaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'codigo'         => $this->codigo,
            'cliente'        => [
                'id'      => $this->cliente?->id,
                'nombre'  => $this->cliente?->nombre,
                'documento' => $this->cliente?->documento,
            ],
            'usuario'        => [
                'id'   => $this->usuario?->id,
                'name' => $this->usuario?->name,
            ],
            'tipo_venta'     => $this->tipoVenta?->nombre,
            'forma_pago'     => $this->formaPago?->nombre,
            'estado'         => [
                'codigo' => $this->estado?->codigo,
                'nombre' => $this->estado?->nombre,
                'color'  => $this->estado?->color,
            ],
            'subtotal'       => $this->subtotal,
            'impuestos'      => $this->impuestos,
            'descuentos'     => $this->descuentos,
            'total'          => $this->total,
            'total_pagado'   => $this->total_pagado,
            'saldo_pendiente'=> $this->saldo_pendiente,
            'observaciones'  => $this->observaciones,
            'fecha_emision' => $this->fecha_emision ? (method_exists($this->fecha_emision, 'format') ? $this->fecha_emision->format('Y-m-d H:i:s') : (string) $this->fecha_emision) : null,

            'prefijo'        => $this->prefijo,
            'consecutivo'    => $this->consecutivo,
            'detalles'       => FacturaDetalleResource::collection($this->whenLoaded('detalles')),
            'pagos'          => PagoFacturaResource::collection($this->whenLoaded('pagos')),
            'auditorias'     => FacturaAuditoriaResource::collection($this->whenLoaded('auditorias')),
        ];
    }
}
