<?php

namespace App\Http\Requests\Parametros\EstadosOrdenServicio;

use Illuminate\Foundation\Http\FormRequest;

class StoreEstadoOrdenServicioRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'nombre' => ['required','string','max:50','unique:estados_orden_servicio,nombre'],
        ];
    }
    protected function prepareForValidation(): void
    {
        if ($this->has('nombre')) $this->merge(['nombre'=>trim(mb_strtoupper($this->input('nombre')))]);
    }
}
