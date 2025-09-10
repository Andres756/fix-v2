<?php

namespace App\Http\Requests\Parametros\AplicacionDescuento;

use Illuminate\Foundation\Http\FormRequest;

class StoreAplicacionDescuentoRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'nombre' => ['required','string','max:30','unique:aplicacion_descuento,nombre'],
            'activo' => ['sometimes','boolean'],
        ];
    }
    protected function prepareForValidation(): void
    {
        if ($this->has('nombre')) $this->merge(['nombre'=>trim(mb_strtoupper($this->input('nombre')))]);
    }
}
