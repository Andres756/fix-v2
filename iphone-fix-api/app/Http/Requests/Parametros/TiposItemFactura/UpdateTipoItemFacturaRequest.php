<?php

namespace App\Http\Requests\Parametros\TiposItemFactura;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTipoItemFacturaRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $id = $this->route('tipo_item_factura');
        return [
            'nombre' => ['sometimes','string','max:30',"unique:tipos_item_factura,nombre,{$id}"],
            'activo' => ['sometimes','boolean'],
        ];
    }
    protected function prepareForValidation(): void
    {
        if ($this->has('nombre')) $this->merge(['nombre'=>trim(mb_strtoupper($this->input('nombre')))]);
    }
}
