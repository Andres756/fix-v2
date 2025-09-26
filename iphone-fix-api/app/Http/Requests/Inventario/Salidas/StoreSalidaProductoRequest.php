<?php
// app/Http/Requests/Inventario/Salidas/StoreSalidaProductoRequest.php

namespace App\Http\Requests\Inventario\Salidas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Inventario\Inventario;

class StoreSalidaProductoRequest extends FormRequest 
{
    public function authorize(): bool { return true; }
    
    public function rules(): array 
    {
        return [
            'inventario_id' => ['required', 'integer', 'exists:inventarios,id'],
            'tipo_salida'   => ['required', 'in:venta,orden_servicio,ajuste,perdida'],
            'cantidad'      => ['required', 'integer', 'min:1'],
            'referencia_id' => ['nullable', 'integer'],
            'fecha_salida'  => ['required', 'date'],
            'observaciones' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if (!$this->inventario_id || !$this->cantidad) {
                return;
            }

            $inventario = Inventario::find($this->inventario_id);
            
            if (!$inventario) {
                return;
            }

            // Validar stock disponible
            if ($this->cantidad > $inventario->stock) {
                $validator->errors()->add(
                    'cantidad', 
                    "Stock insuficiente. Disponible: {$inventario->stock} unidades."
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'inventario_id.required' => 'El producto es obligatorio',
            'tipo_salida.required' => 'El tipo de salida es obligatorio',
            'tipo_salida.in' => 'Tipo de salida inválido',
            'cantidad.required' => 'La cantidad es obligatoria',
            'cantidad.min' => 'La cantidad debe ser mayor a 0',
            'fecha_salida.required' => 'La fecha de salida es obligatoria',
        ];
    }
}