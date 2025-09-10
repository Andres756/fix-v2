<?php

namespace App\Http\Requests\Parametros\EstadosFactura;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEstadoFacturaRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $id = $this->route('estado_factura');
        return [
            'nombre' => ['sometimes','string','max:30',"unique:estados_factura,nombre,{$id}"],
            'activo' => ['sometimes','boolean'],
        ];
    }
    protected function prepareForValidation(): void
    {
        if ($this->has('nombre')) $this->merge(['nombre'=>trim(mb_strtoupper($this->input('nombre')))]);
    }
}
