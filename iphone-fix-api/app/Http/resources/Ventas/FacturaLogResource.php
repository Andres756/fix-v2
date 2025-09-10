<?php
// app/Http/Resources/Ventas/FacturaLogResource.php
namespace App\Http\Resources\Ventas;

use Illuminate\Http\Resources\Json\JsonResource;

class FacturaLogResource extends JsonResource
{
    public function toArray($req)
    {
        return [
            'id'         => $this->id,
            'factura_id' => $this->factura_id,
            'accion_id'  => $this->accion_id,
            'usuario_id' => $this->usuario_id,
            'detalle'    => $this->detalle,
            'created_at' => optional($this->created_at)->toDateTimeString(),
        ];
    }
}
