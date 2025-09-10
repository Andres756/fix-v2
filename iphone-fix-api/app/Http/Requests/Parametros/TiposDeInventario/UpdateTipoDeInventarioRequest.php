<?php

namespace App\Http\Requests\Parametros\TiposDeInventario;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTipoDeInventarioRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $id = $this->route('tipo_de_inventario');
        return [
            'nombre' => ['sometimes','string','max:50',"unique:tipos_de_inventario,nombre,{$id}"],
        ];
    }
    protected function prepareForValidation(): void
    {
        if ($this->has('nombre')) $this->merge(['nombre'=>trim(mb_strtoupper($this->input('nombre')))]);
    }
}
