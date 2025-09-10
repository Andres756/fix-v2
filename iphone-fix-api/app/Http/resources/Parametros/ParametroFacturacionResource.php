<?php

namespace App\Http\Resources\Parametros;

use Illuminate\Http\Resources\Json\JsonResource;

class ParametroFacturacionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'clave'       => $this->clave,
            'valor'       => $this->valor,
            'descripcion' => $this->descripcion,
            'activo'      => (bool)$this->activo,
        ];
    }
}
