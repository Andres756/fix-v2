<?php

namespace App\Http\Requests\PlanSepare;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanSepareRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // puedes agregar lógica de permisos luego
    }

    public function rules(): array
    {
        return [
            'cliente_id'        => 'required|integer|exists:clientes,id',
            'inventario_id'     => 'required|integer|exists:inventarios,id',
            'precio_total'      => 'required|numeric|min:1',
            'porcentaje_minimo' => 'nullable|numeric|min:1|max:100',
            'abono_inicial'     => 'nullable|numeric|min:0',
            'observaciones'     => 'nullable|string|max:255',
        ];
    }
}
