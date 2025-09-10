<?php

namespace App\Http\Resources\Parametros;

use Illuminate\Http\Resources\Json\JsonResource;

class EstadoInventarioResource extends JsonResource
{
    public function toArray($request)
    {
        return ['id'=>$this->id,'nombre'=>$this->nombre,'mostrar_en_stock'=>(bool)$this->mostrar_en_stock];
    }
}
