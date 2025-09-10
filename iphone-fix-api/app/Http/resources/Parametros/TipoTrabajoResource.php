<?php
// app/Http/Resources/Parametros/TipoTrabajoResource.php
namespace App\Http\Resources\Parametros;

use Illuminate\Http\Resources\Json\JsonResource;

class TipoTrabajoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                  => $this->id,
            'nombre'              => $this->nombre,
            'descripcion'         => $this->descripcion,
            'costo_sugerido'       => $this->costo_sugerido !== null ? (float)$this->costo_sugerido : null,
            'tipo_pago_tecnico'   => $this->tipo_pago_tecnico, // 'porcentaje' | 'valor_fijo'
            'valor_pago_tecnico'  => $this->valor_pago_tecnico !== null ? (float)$this->valor_pago_tecnico : null,
            'created_at'          => optional($this->created_at)?->toISOString(),
            'updated_at'          => optional($this->updated_at)?->toISOString(),
        ];
    }
}
