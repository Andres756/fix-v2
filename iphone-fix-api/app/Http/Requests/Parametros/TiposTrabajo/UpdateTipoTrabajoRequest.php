<?php

namespace App\Http\Requests\Parametros\TiposTrabajo;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTipoTrabajoRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $id = $this->route('tipo_trabajo');
        return [
            'nombre'             => ['sometimes','string','max:100',"unique:tipos_trabajo,nombre,{$id}"],
            'descripcion'        => ['sometimes','nullable','string'],
            'costo_sugerido'      => ['sometimes','numeric','min:0'],
            'tipo_pago_tecnico'  => ['sometimes','in:porcentaje,valor_fijo'],
            'valor_pago_tecnico' => ['sometimes','numeric','min:0'],
        ];
    }
    protected function prepareForValidation(): void
    {
        if ($this->has('nombre')) $this->merge(['nombre'=>trim(mb_strtoupper($this->input('nombre')))]);
    }
}
