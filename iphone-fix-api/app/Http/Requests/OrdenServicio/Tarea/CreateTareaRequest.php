<?php

namespace App\Http\Requests\OrdenServicio\Tarea;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTareaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'equipo_os_id'    => ['prohibited'], // viene por la ruta
            'tipo_trabajo_id' => ['required','integer','exists:tipos_trabajo,id'],
            'costo_aplicado'  => 'required|numeric|min:0', // 👈 así lo aceptas
            'estado'          => ['sometimes', Rule::in(['pendiente','en_proceso','completada','cancelada'])],
            'observaciones'   => ['nullable','string'],
        ];
    }
}
