<?php

namespace App\Http\Requests\Facturacion;

use Illuminate\Foundation\Http\FormRequest;

class RegistrarPagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Si usas políticas de acceso, cámbialo a verificación de roles o permisos.
        return true;
    }

    public function rules(): array
    {
        return [
            'forma_pago_id' => 'nullable|exists:formas_pago,id',
            'valor' => 'required|numeric|min:0.01',
            'referencia_externa' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'valor.required' => 'Debe indicar el valor del pago.',
            'valor.min' => 'El valor del pago debe ser mayor que cero.',
            'forma_pago_id.exists' => 'La forma de pago seleccionada no es válida.',
        ];
    }
}
