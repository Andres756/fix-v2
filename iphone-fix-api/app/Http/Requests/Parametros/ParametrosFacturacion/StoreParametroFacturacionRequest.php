<?php

namespace App\Http\Requests\Parametros\ParametrosFacturacion;

use Illuminate\Foundation\Http\FormRequest;

class StoreParametroFacturacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'clave'       => ['required','string','max:100','unique:parametros_facturacion,clave'],
            'valor'       => ['required','string','max:255'],
            'descripcion' => ['sometimes','nullable','string'],
            'activo'      => ['sometimes','boolean'],
        ];
    }
    protected function prepareForValidation(): void
    {
        if ($this->has('clave')) $this->merge(['clave'=>trim(mb_strtoupper($this->input('clave')))]);
    }
}
