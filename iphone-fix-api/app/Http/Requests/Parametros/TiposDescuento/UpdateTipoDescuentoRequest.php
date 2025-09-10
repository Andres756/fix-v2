<?php

namespace App\Http\Requests\Parametros\TiposDescuento;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTipoDescuentoRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $id = $this->route('tipo_descuento');
        return [
            'nombre' => ['sometimes','string','max:30',"unique:tipos_descuento,nombre,{$id}"],
            'activo' => ['sometimes','boolean'],
        ];
    }
    protected function prepareForValidation(): void
    {
        if ($this->has('nombre')) $this->merge(['nombre'=>trim(mb_strtoupper($this->input('nombre')))]);
    }
}
