<?php
// app/Http/Requests/Inventario/Salidas/UpdateSalidaProductoRequest.php

namespace App\Http\Requests\Inventario\Salidas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSalidaProductoRequest extends FormRequest 
{
    public function authorize(): bool { return true; }
    
    public function rules(): array 
    {
        return [
            'observaciones' => ['sometimes', 'nullable', 'string'],
            'fecha_salida'  => ['sometimes', 'date'],
            
            // NO permitir editar cantidad, tipo_salida o inventario_id
            // Esos campos afectan el stock y podrían descuadrar el inventario
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_salida.date' => 'La fecha de salida debe ser válida',
        ];
    }
}