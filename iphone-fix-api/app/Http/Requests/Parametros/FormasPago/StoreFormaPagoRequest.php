<?php

namespace App\Http\Requests\Parametros\FormasPago;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormaPagoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre' => ['required','string','max:50','unique:formas_pago,nombre'],
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
