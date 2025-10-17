<?php

namespace App\Http\Requests\Inventario\Inventarios;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'], // ✅ Ya no es único
            'nombre_detallado' => ['required', 'string', 'max:255', 'unique:inventarios,nombre_detallado'], // ✅ Obligatorio y único
            'codigo' => ['required', 'string', 'max:100', 'unique:inventarios,codigo'], // ✅ Sigue siendo único

            'tipo_inventario_id' => ['required', 'integer', 'exists:tipos_de_inventario,id'],
            'categoria_id'       => ['required', 'integer', 'exists:categorias,id'],

            'stock_minimo' => ['required', 'integer', 'min:1'], 
            'precio'       => ['required', 'numeric', 'gt:0'],
            'costo_mayor'  => ['required', 'numeric', 'gt:0'],

            'tipo_impuesto'  => ['nullable', 'in:n/a,porcentaje,fijo'],
            'valor_impuesto' => ['nullable', 'numeric', 'min:0'],
            'notas'          => ['nullable', 'string'],

            'imagen' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],

            // Detalles por tipo
            'detalle_equipo.imei_1'            => ['nullable', 'string', 'max:100'],
            'detalle_equipo.imei_2'            => ['nullable', 'string', 'max:100'],
            'detalle_equipo.estado_fisico'     => ['nullable', 'string', 'max:100'],
            'detalle_equipo.version_ios'       => ['nullable', 'string', 'max:100'],
            'detalle_equipo.almacenamiento'    => ['nullable', 'string', 'max:100'],
            'detalle_equipo.color'             => ['nullable', 'string', 'max:100'],

            'detalle_producto.material'        => ['nullable', 'string', 'max:100'],
            'detalle_producto.compatibilidad'  => ['nullable', 'string', 'max:100'],
            'detalle_producto.tipo_accesorio'  => ['nullable', 'string', 'max:100'],

            'detalle_repuesto.modelo_compatible'     => ['nullable', 'string', 'max:100'],
            'detalle_repuesto.tipo_repuesto'         => ['nullable', 'string', 'max:100'],
            'detalle_repuesto.referencia_fabricante' => ['nullable', 'string', 'max:100'],
            'detalle_repuesto.garantia_meses'        => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_detallado.required' => 'El nombre detallado es obligatorio',
            'nombre_detallado.unique'   => 'El nombre detallado ya existe',
            'codigo.unique'             => 'El código ya existe',
            'precio.gt'                 => 'El precio de venta debe ser mayor a 0',
            'costo_mayor.gt'            => 'El precio al por mayor debe ser mayor a 0',
            'stock_minimo.required'     => 'El stock mínimo es obligatorio',
            'stock_minimo.min'          => 'El stock mínimo debe ser al menos 1',
        ];
    }
}
