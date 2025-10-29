<?php

namespace App\Http\Resources\Facturacion;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class FacturaAuditoriaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'accion'    => $this->accion,
            'detalle'   => $this->detalle,
            'usuario'   => $this->usuario?->name,
            'fecha'     => $this->created_at
                ? (method_exists($this->created_at, 'format')
                    ? $this->created_at->format('Y-m-d H:i:s')
                    : Carbon::parse($this->created_at)->format('Y-m-d H:i:s'))
                : null,
        ];
    }
}
