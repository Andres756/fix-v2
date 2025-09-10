<?php

namespace App\Http\Requests\Parametros\TiposCambioPlanSepare;

use Illuminate\Foundation\Http\FormRequest;

class StoreTipoCambioPlanSepareRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'nombre' => ['required','string','max:50','unique:tipos_cambio_plan_separe,nombre'],
            'activo' => ['sometimes','boolean'],
        ];
    }
    protected function prepareForValidation(): void
    {
        if ($this->has('nombre')) $this->merge(['nombre'=>trim(mb_strtoupper($this->input('nombre')))]);
    }
}
