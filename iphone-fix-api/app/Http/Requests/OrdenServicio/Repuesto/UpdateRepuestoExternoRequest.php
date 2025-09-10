<?php

namespace App\Http\Requests\OrdenServicio\Repuesto;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRepuestoExternoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'descripcion' => 'sometimes|required|string|max:255',
            'cantidad' => 'sometimes|required|integer|min:1',
            'costo_unitario' => 'sometimes|required|numeric|min:0',
            'proveedor_id' => 'nullable|exists:proveedores,id', // Cambiado de 'proveedor' a 'proveedor_id'
            'observaciones' => 'nullable|string',
            'fecha_gasto' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'descripcion.required' => 'La descripción es obligatoria.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.min' => 'La cantidad debe ser mayor a 0.',
            'costo_unitario.required' => 'El costo unitario es obligatorio.',
            'costo_unitario.min' => 'El costo unitario debe ser mayor o igual a 0.',
            'proveedor_id.exists' => 'El proveedor seleccionado no existe.',
            'fecha_gasto.date' => 'La fecha de gasto debe ser una fecha válida.',
        ];
    }
}