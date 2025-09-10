<?php

namespace App\Http\Requests\OrdenServicio\Equipo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateEquipoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // viene por la ruta, no por el body
            'orden_id'               => ['prohibited'],

            'imei_serial'            => ['nullable','string','max:100'],
            'marca'                  => ['nullable','string','max:100'],
            'modelo'                 => ['required','string','max:100'],
            'descripcion_problema'   => ['nullable','string'],
            'contrasena_equipo'      => ['nullable','string','max:50'],
            'valor_estimado'         => ['nullable','numeric','min:0'],
            'fecha_estimada_entrega' => ['nullable','date'],

            'tecnico_asignado'       => ['nullable','integer','exists:users,id'],

            'comision_habilitada'    => ['sometimes','boolean'],
            'tipo_comision'          => ['required_if:comision_habilitada,1', Rule::in(['porcentaje','fijo']), 'nullable'],
            'valor_comision'         => ['required_if:comision_habilitada,1','nullable','numeric','min:0'],

            'estado'                 => ['sometimes', Rule::in(['pendiente','en_proceso','finalizado','cancelado'])],
            'observaciones'          => ['nullable','string'],
            'fecha_finalizacion'     => ['nullable','date'],
        ];
    }
}
