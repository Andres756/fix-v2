<?php

namespace App\Http\Requests\Parametros\TiposVenta;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTipoVentaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('tipo_venta'); // por parameters([...])
        return [
            'nombre' => ['sometimes','string','max:20',"unique:tipos_venta,nombre,{$id}"],
            'activo' => ['sometimes','boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('nombre')) {
            $this->merge(['nombre' => trim(mb_strtoupper($this->input('nombre')))]);
        }
    }
}
