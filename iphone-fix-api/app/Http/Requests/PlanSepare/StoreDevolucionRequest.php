<?php

namespace App\Http\Requests\PlanSepare;

use Illuminate\Foundation\Http\FormRequest;

class StoreDevolucionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'valor_devolucion' => 'required|numeric|min:1',
            'motivo'           => 'nullable|string|max:255',
            'observaciones'    => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'valor_devolucion.required' => 'Debe especificar el valor a devolver.',
            'valor_devolucion.numeric'  => 'El valor de devolución debe ser numérico.',
            'valor_devolucion.min'      => 'El valor de devolución debe ser mayor que cero.'
        ];
    }
}
