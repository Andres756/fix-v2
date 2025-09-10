<?php

namespace App\Http\Requests\Parametros\AccionesFacturaLog;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccionFacturaLogRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'nombre' => ['required','string','max:30','unique:acciones_factura_log,nombre'],
            'activo' => ['sometimes','boolean'],
        ];
    }
    protected function prepareForValidation(): void
    {
        if ($this->has('nombre')) $this->merge(['nombre'=>trim(mb_strtoupper($this->input('nombre')))]);
    }
}
