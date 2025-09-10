<?php

namespace App\Http\Requests\Parametros\TiposDeInventario;

use Illuminate\Foundation\Http\FormRequest;

class StoreTipoDeInventarioRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'nombre' => ['required','string','max:50','unique:tipos_de_inventario,nombre'],
        ];
    }
    protected function prepareForValidation(): void
    {
        if ($this->has('nombre')) $this->merge(['nombre'=>trim(mb_strtoupper($this->input('nombre')))]);
    }
}
