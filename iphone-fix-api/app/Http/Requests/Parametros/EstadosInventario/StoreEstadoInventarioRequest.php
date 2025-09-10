<?php

namespace App\Http\Requests\Parametros\EstadosInventario;

use Illuminate\Foundation\Http\FormRequest;

class StoreEstadoInventarioRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'nombre' => ['required','string','max:50','unique:estados_inventario,nombre'],
            'mostrar_en_stock' => ['sometimes','boolean'],
        ];
    }
    protected function prepareForValidation(): void
    {
        if ($this->has('nombre')) $this->merge(['nombre'=>trim(mb_strtoupper($this->input('nombre')))]);
    }
}
