<?php

namespace App\Http\Requests\OrdenServicio\Tarea;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEstadoTareaRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Aquí podrías validar que el usuario logueado sea técnico, 
        // o esté autorizado a cambiar este estado. Por ahora lo dejamos abierto.
        return true;
    }

    public function rules(): array
    {
        return [
            'estado' => [
                'required',
                Rule::in(['pendiente','en_proceso','completada','cancelada']),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser uno de: pendiente, en_proceso, completada o cancelada.',
        ];
    }
}
