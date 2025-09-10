<?php

namespace App\Http\Requests\Parametros\AccionesFacturaLog;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccionFacturaLogRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $id = $this->route('accion_factura_log');
        return [
            'nombre' => ['sometimes','string','max:30',"unique:acciones_factura_log,nombre,{$id}"],
            'activo' => ['sometimes','boolean'],
        ];
    }
    protected function prepareForValidation(): void
    {
        if ($this->has('nombre')) $this->merge(['nombre'=>trim(mb_strtoupper($this->input('nombre')))]);
    }
}
