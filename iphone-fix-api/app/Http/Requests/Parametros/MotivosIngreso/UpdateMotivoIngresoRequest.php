<?php

namespace App\Http\Requests\Parametros\MotivosIngreso;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMotivoIngresoRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $id = $this->route('motivo_ingreso');
        return [
            'nombre' => ['sometimes','string','max:100',"unique:motivos_ingreso,nombre,{$id}"],
        ];
    }
    protected function prepareForValidation(): void
    {
        if ($this->has('nombre')) $this->merge(['nombre'=>trim(mb_strtoupper($this->input('nombre')))]);
    }
}
