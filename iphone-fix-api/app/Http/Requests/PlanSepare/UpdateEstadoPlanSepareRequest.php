<?php

namespace App\Http\Requests\PlanSepare;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEstadoPlanSepareRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Puedes personalizar esto luego si usas roles
        return true;
    }

    public function rules(): array
    {
        return [
            // Puedes aceptar estado_id o estado_codigo, según tu API
            'estado_id' => 'nullable|integer|exists:estados_plan_separe,id',
            'estado_codigo' => 'nullable|string|exists:estados_plan_separe,codigo',
            'observaciones' => 'nullable|string|max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'estado_id.exists' => 'El estado_id no existe en la base de datos.',
            'estado_codigo.exists' => 'El código de estado no existe en la base de datos.'
        ];
    }
}
