<?php

namespace App\Http\Requests\Parametros\EstadosOrdenServicio;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEstadoOrdenServicioRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $id = $this->route('estado_orden_servicio');
        return [
            'nombre' => ['sometimes','string','max:50',"unique:estados_orden_servicio,nombre,{$id}"],
        ];
    }
    protected function prepareForValidation(): void
    {
        if ($this->has('nombre')) $this->merge(['nombre'=>trim(mb_strtoupper($this->input('nombre')))]);
    }
}
