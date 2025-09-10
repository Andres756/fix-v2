<?php

namespace App\Http\Requests\Clientes;

use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'    => 'required|string|max:255',
            'documento' => 'required|string|max:50|unique:clientes,documento',
            'telefono'  => 'required|string|max:20',
            'correo'    => 'required|email|max:150',
            'direccion' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'    => 'El nombre es obligatorio.',
            'documento.required' => 'El documento es obligatorio.',
            'documento.unique'   => 'Ya existe un cliente con este número de documento.',
            'telefono.required'  => 'El teléfono es obligatorio.',
            'correo.required'    => 'El correo es obligatorio.',
            'correo.email'       => 'El correo debe tener un formato válido.',
            'direccion.required' => 'La dirección es obligatoria.',
        ];
    }
}
