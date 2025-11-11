<?php
namespace App\Http\Resources\Inventario;

use Illuminate\Http\Resources\Json\JsonResource;

class ModeloEquipoResource extends JsonResource
{
    public function toArray($req)
    {
        return [
            'id'     => $this->id,
            'nombre' => $this->nombre,  // O cualquier otro campo que desees mostrar del modelo
            'marca'  => $this->marca,   // Ejemplo de campo adicional
            // Agrega más campos si es necesario
        ];
    }
}
