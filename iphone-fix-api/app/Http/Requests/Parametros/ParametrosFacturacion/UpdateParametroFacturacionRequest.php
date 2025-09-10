<?php

namespace App\Http\Requests\Parametros\ParametrosFacturacion;

use Illuminate\Foundation\Http\FormRequest;

class UpdateParametroFacturacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $id = $this->route('parametro_facturacion');
        return [
            'clave'       => ['sometimes','string','max:100',"unique:parametros_facturacion,clave,{$id}"],
            'valor'       => ['sometimes','string','max:255'],
            'descripcion' => ['sometimes','nullable','string'],
            'activo'      => ['sometimes','boolean'],
        ];
    }
    protected function prepareForValidation(): void
    {
        if ($this->has('clave')) $this->merge(['clave'=>trim(mb_strtoupper($this->input('clave')))]);
    }
}
