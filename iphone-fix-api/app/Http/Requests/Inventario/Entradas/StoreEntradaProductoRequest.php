<?php
// app/Http/Requests/Inventario/Entradas/StoreEntradaProductoRequest.php

namespace App\Http\Requests\Inventario\Entradas;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Inventario\Inventario;

class StoreEntradaProductoRequest extends FormRequest 
{
    public function authorize(): bool { return true; }
    
    public function rules(): array 
    {
        return [
            'inventario_id'     => ['required', 'integer', 'exists:inventarios,id'],
            'lote_id'           => ['required', 'integer', 'exists:lotes,id'],
            'motivo_ingreso_id' => ['required', 'integer', 'exists:motivos_ingreso,id'],
            'cantidad'          => ['required', 'integer', 'min:1'],
            'costo_unitario'    => ['required', 'numeric', 'min:0'],
            'fecha_entrada'     => ['required', 'date'],
            'observaciones'     => ['nullable', 'string'],
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

            // Validar si es EQUIPO: solo puede ingresar 1 unidad
            if ($inventario->tipo_inventario_id == 1 && $this->cantidad > 1) {
                $validator->errors()->add(
                    'cantidad', 
                    'Los equipos solo permiten ingresar 1 unidad a la vez.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'lote_id.required' => 'El lote es obligatorio',
            'motivo_ingreso_id.required' => 'El motivo de ingreso es obligatorio',
            'costo_unitario.required' => 'El costo unitario es obligatorio',
            'fecha_entrada.required' => 'La fecha de entrada es obligatoria',
        ];
    }
}