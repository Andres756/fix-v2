<?php

namespace App\Http\Requests\OrdenServicio\Repuesto;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Inventario\Inventario;
use App\Models\OrdenServicio\RepuestoOsInventario;

class UpdateRepuestoInventarioRequest extends FormRequest
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
            'cantidad' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
            ],
            'observaciones' => 'sometimes|nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad debe ser mayor a 0.',
            'observaciones.max' => 'Las observaciones no pueden exceder 500 caracteres.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Solo validar si se está actualizando la cantidad
            if (!$this->has('cantidad')) {
                return;
            }

            $nuevaCantidad = $this->cantidad;
            $repuestoId = $this->route('repuestoId');
            
            if (!$repuestoId) {
                return;
            }

            // Buscar el repuesto actual
            $repuesto = RepuestoOsInventario::find($repuestoId);
            if (!$repuesto) {
                $validator->errors()->add('cantidad', 'El repuesto no existe.');
                return;
            }

            $cantidadOriginal = $repuesto->cantidad;
            $diferencia = $nuevaCantidad - $cantidadOriginal;

            // Solo validar si necesita más stock
            if ($diferencia > 0) {
                $inventario = $repuesto->inventario;
                
                if (!$inventario) {
                    $validator->errors()->add('cantidad', 'No se pudo obtener la información del inventario.');
                    return;
                }

                if ($diferencia > $inventario->stock) {
                    $validator->errors()->add('cantidad', 
                        "No hay suficiente stock. Cantidad actual: {$cantidadOriginal}, " .
                        "Stock disponible: {$inventario->stock}, " .
                        "Máximo que puede usar: " . ($cantidadOriginal + $inventario->stock)
                    );
                }
            }
        });
    }

    /**
     * Obtener datos validados personalizados
     */
    public function getValidatedData(): array
    {
        $data = [];
        
        if ($this->has('cantidad')) {
            $data['cantidad'] = $this->cantidad;
        }
        
        if ($this->has('observaciones')) {
            $data['observaciones'] = $this->observaciones;
        }
        
        return $data;
    }
}