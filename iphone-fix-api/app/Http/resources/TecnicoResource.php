<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TecnicoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'     => $this->id,
            'nombre' => $this->name,   // suponiendo que en `users` tienes `name`
            'email'  => $this->email,
        ];
    }
}
