<?php
// app/Http/Requests/Inventario/Inventarios/UpdateInventarioRequest.php

namespace App\Http\Requests\Inventario\Inventarios;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInventarioRequest extends FormRequest 
{
    public function authorize(): bool { return true; }
    
    public function rules(): array 
    {
        return [
            'nombre'         => ['sometimes', 'string', 'max:255'],
            'nombre_detallado' => ['sometimes', 'nullable', 'string', 'max:255'],
            'codigo'         => ['sometimes', 'nullable', 'string', 'max:100'],
            'categoria_id'   => ['sometimes', 'nullable', 'integer', 'exists:categorias,id'],
            'tipo_inventario_id' => ['sometimes', 'integer', 'exists:tipos_de_inventario,id'],
            
            // ELIMINADOS: lote_id, proveedor_id, estado_inventario_id, stock, costo, activo, fecha_ingreso
            
            'stock_minimo'   => ['sometimes', 'integer', 'min:1'],
            'precio'         => ['sometimes', 'numeric', 'gt:0'],
            'costo_mayor'    => ['sometimes', 'numeric', 'gt:0'],
            
            'tipo_impuesto'  => ['sometimes', 'in:n/a,porcentaje,fijo'],
            'valor_impuesto' => ['sometimes', 'numeric', 'min:0'],
            'notas'          => ['sometimes', 'nullable', 'string'],
            
            'imagen' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'precio.gt'      => 'El precio de venta debe ser mayor a 0',
            'costo_mayor.gt' => 'El precio al mayor debe ser mayor a 0',
            'stock_minimo.min' => 'El stock mínimo debe ser al menos 1',
        ];
    }
}