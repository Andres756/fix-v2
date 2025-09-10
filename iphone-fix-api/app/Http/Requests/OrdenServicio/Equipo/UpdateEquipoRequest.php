<?php

namespace App\Http\Requests\OrdenServicio\Equipo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEquipoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'orden_id'               => ['prohibited'],

            'imei_serial'            => ['sometimes','nullable','string','max:100'],
            'marca'                  => ['sometimes','nullable','string','max:100'],
            'modelo'                 => ['sometimes','required','string','max:100'],
            'descripcion_problema'   => ['sometimes','nullable','string'],
            'contrasena_equipo'      => ['sometimes','nullable','string','max:50'],
            'valor_estimado'         => ['sometimes','nullable','numeric','min:0'],
            'fecha_estimada_entrega' => ['sometimes','nullable','date'],

            'tecnico_asignado'       => ['sometimes','nullable','integer','exists:users,id'],

            'comision_habilitada'    => ['sometimes','boolean'],
            'tipo_comision'          => ['sometimes','nullable', Rule::in(['porcentaje','fijo'])],
            'valor_comision'         => ['sometimes','nullable','numeric','min:0'],

            'estado'                 => ['sometimes', Rule::in(['pendiente','en_proceso','finalizado','cancelado'])],
            'observaciones'          => ['sometimes','nullable','string'],
            'fecha_finalizacion'     => ['sometimes','nullable','date'],
        ];
    }
}
