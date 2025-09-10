<?php

namespace App\Http\Requests\Parametros\FormasPago;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFormaPagoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('forma_pago'); // lo definimos en routes->parameters()
        return [
            'nombre' => ['sometimes','string','max:50',"unique:formas_pago,nombre,{$id}"],
            'activo' => ['sometimes','boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('nombre')) {
            $this->merge(['nombre' => trim(mb_strtoupper($this->input('nombre')))]);
        }
    }
}
