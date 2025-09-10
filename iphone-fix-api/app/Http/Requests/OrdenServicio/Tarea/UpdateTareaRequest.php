<?php

namespace App\Http\Requests\OrdenServicio\Tarea;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTareaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'equipo_os_id'    => ['prohibited'],
            // si cambia el tipo de trabajo, debemos recibirlo y recalculamos costo
            'tipo_trabajo_id' => ['sometimes','required','integer','exists:tipos_trabajo,id'],
            'costo_aplicado'  => 'required|numeric|min:0', // 👈 así lo aceptas
            'estado'          => ['sometimes', Rule::in(['pendiente','en_proceso','completada','cancelada'])],
            'observaciones'   => ['sometimes','nullable','string'],
        ];
    }
}
