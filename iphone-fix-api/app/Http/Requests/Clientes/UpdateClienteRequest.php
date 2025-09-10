<?php

namespace App\Http\Requests\Clientes;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $clienteId = $this->route('cliente'); // ← id dinámico desde la ruta

        return [
            'nombre'    => 'required|string|max:255',
            'documento' => 'required|string|max:50|unique:clientes,documento,' . $clienteId,
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
            'documento.unique'   => 'Ya existe otro cliente con este número de documento.',
            'telefono.required'  => 'El teléfono es obligatorio.',
            'correo.required'    => 'El correo es obligatorio.',
            'correo.email'       => 'El correo debe tener un formato válido.',
            'direccion.required' => 'La dirección es obligatoria.',
        ];
    }
}
