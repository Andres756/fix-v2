<?php

namespace App\Http\Requests\OrdenServicio\OrdenServicio;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrdenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ❌ quitamos cliente_id, ya viene por la ruta
            'observaciones_generales' => ['nullable', 'string'],
            'fecha_creacion'          => ['nullable', 'date'],
            'fecha_cierre'            => ['nullable', 'date'],
            'estado'                  => ['nullable', 'in:pendiente,recibida,en_proceso,finalizada,cancelada'],
        ];
    }
}
