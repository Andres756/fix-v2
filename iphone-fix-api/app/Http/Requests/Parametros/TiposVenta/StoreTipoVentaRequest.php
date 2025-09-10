<?php

namespace App\Http\Requests\Parametros\TiposVenta;

use Illuminate\Foundation\Http\FormRequest;

class StoreTipoVentaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre' => ['required','string','max:20','unique:tipos_venta,nombre'],
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
