<?php

namespace App\Http\Requests\Parametros\TiposTrabajo;

use Illuminate\Foundation\Http\FormRequest;

class StoreTipoTrabajoRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'nombre'             => ['required','string','max:100','unique:tipos_trabajo,nombre'],
            'descripcion'        => ['sometimes','nullable','string'],
            'costo_sugerido'      => ['required','numeric','min:0'],
            'tipo_pago_tecnico'  => ['required','in:porcentaje,valor_fijo'],
            'valor_pago_tecnico' => ['required','numeric','min:0'],
        ];
    }
    protected function prepareForValidation(): void
    {
        if ($this->has('nombre')) $this->merge(['nombre'=>trim(mb_strtoupper($this->input('nombre')))]);
    }
}
