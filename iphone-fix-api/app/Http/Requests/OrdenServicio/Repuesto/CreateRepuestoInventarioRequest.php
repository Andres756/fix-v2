<?php

namespace App\Http\Requests\OrdenServicio\Repuesto;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Inventario\Inventario;

class CreateRepuestoInventarioRequest extends FormRequest
{
    /**
     * Tipo de inventario para repuestos
     */
    private const TIPO_REPUESTO = 3;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'inventario_id' => [
                'required',
                'exists:inventarios,id',
            ],
            'cantidad' => [
                'required',
                'integer',
                'min:1',
            ],
            'observaciones' => 'nullable|string|max:500',
            // No se incluye costo_total porque se calcula automáticamente
            // No se incluye costo_unitario porque se toma del precio del inventario
        ];
    }

    public function messages(): array
    {
        return [
            'inventario_id.required' => 'Debe seleccionar un repuesto del inventario.',
            'inventario_id.exists' => 'El repuesto seleccionado no existe.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad debe ser mayor a 0.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if (!$this->inventario_id || !$this->cantidad) {
                return;
            }

            $inventario = Inventario::where('id', $this->inventario_id)
                ->where('tipo_inventario_id', self::TIPO_REPUESTO)
                ->first();
            
            if (!$inventario) {
                $validator->errors()->add('inventario_id', 'Solo se pueden seleccionar repuestos del inventario.');
                return;
            }

            // Validar stock disponible
            if ($inventario->stock <= 0) {
                $validator->errors()->add('inventario_id', 'El repuesto seleccionado no tiene stock disponible.');
            } elseif ($this->cantidad > $inventario->stock) {
                $validator->errors()->add('cantidad', "La cantidad solicitada ({$this->cantidad}) excede el stock disponible ({$inventario->stock}).");
            }
        });
    }

    /**
     * Obtener el inventario validado
     */
    public function getInventario(): ?Inventario
    {
        return Inventario::where('id', $this->inventario_id)
            ->where('tipo_inventario_id', self::TIPO_REPUESTO)
            ->first();
    }
}