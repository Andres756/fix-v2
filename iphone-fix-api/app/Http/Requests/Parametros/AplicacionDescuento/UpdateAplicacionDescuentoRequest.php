<?php

namespace App\Http\Requests\Parametros\AplicacionDescuento;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAplicacionDescuentoRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $id = $this->route('aplicacion_descuento');
        return [
            'nombre' => ['sometimes','string','max:30',"unique:aplicacion_descuento,nombre,{$id}"],
            'activo' => ['sometimes','boolean'],
        ];
    }
    protected function prepareForValidation(): void
    {
        if ($this->has('nombre')) $this->merge(['nombre'=>trim(mb_strtoupper($this->input('nombre')))]);
    }
}
