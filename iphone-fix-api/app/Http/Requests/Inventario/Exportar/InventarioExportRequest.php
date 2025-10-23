<?php

namespace App\Http\Requests\Inventario\Exportar;

use Illuminate\Foundation\Http\FormRequest;

class InventarioExportRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tipo_inventario_id' => 'required|integer|exists:tipos_de_inventario,id',
            'filtro_stock' => 'nullable|in:todos,sin_stock,con_stock,bajo_minimo',
            'fecha_desde' => 'nullable|date',
            'fecha_hasta' => 'nullable|date|after_or_equal:fecha_desde',
            'activo' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'tipo_inventario_id.required' => 'Debe seleccionar un tipo de inventario.',
            'filtro_stock.in' => 'El filtro de stock no es válido.',
        ];
    }
}
