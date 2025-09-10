<?php

namespace App\Http\Requests\Parametros\MotivosIngreso;

use Illuminate\Foundation\Http\FormRequest;

class StoreMotivoIngresoRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'nombre' => ['required','string','max:100','unique:motivos_ingreso,nombre'],
        ];
    }
    protected function prepareForValidation(): void
    {
        if ($this->has('nombre')) $this->merge(['nombre'=>trim(mb_strtoupper($this->input('nombre')))]);
    }
}
