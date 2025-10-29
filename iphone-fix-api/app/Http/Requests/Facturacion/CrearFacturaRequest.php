<?php

namespace App\Http\Requests\Facturacion;

use Illuminate\Foundation\Http\FormRequest;

class CrearFacturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Puedes agregar aquí lógica de permisos si es necesario.
        return true;
    }

    public function rules(): array
    {
        return [
            'origen' => 'required|in:venta,servicio',

            // --- Campos comunes ---
            'cliente_id' => 'required_if:origen,venta,servicio|exists:clientes,id',
            'forma_pago_id' => 'nullable|exists:formas_pago,id',
            'observaciones' => 'nullable|string|max:500',

            // --- Campos para venta directa ---
            'tipo_venta' => 'nullable|in:DET,MAY',
            'items' => 'required_if:origen,venta|array|min:1',
            'items.*.inventario_id' => 'required_if:origen,venta|exists:inventarios,id',
            'items.*.cantidad' => 'required_if:origen,venta|integer|min:1',
            'items.*.descripcion' => 'nullable|string|max:255',
            'items.*.descuento' => 'nullable|numeric|min:0',
            'items.*.impuesto' => 'nullable|numeric|min:0',

            // --- Campos para servicio técnico ---
            'orden_id' => 'required_if:origen,servicio|exists:ordenes_servicio,id',
        ];
    }

    public function messages(): array
    {
        return [
            'origen.required' => 'Debe especificar el tipo de origen de la factura (venta o servicio).',
            'items.required_if' => 'Debe agregar al menos un ítem a la factura de venta.',
            'cliente_id.required_if' => 'Debe seleccionar un cliente para generar la factura.',
            'orden_id.required_if' => 'Debe indicar la orden de servicio cuando el origen sea servicio.',
        ];
    }
}
