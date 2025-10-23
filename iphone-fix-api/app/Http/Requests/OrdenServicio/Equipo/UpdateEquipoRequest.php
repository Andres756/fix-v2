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
            'imei_serial'            => ['nullable','string','max:100'],
            'marca'                  => ['nullable','string','max:100'],
            'modelo'                 => ['sometimes','required','string','max:100'],
            'descripcion_problema'   => ['nullable','string'],
            'contrasena_equipo'      => ['nullable','string','max:50'],
            'valor_estimado'         => ['nullable','numeric','min:0'],
            'fecha_estimada_entrega' => ['nullable','date'],
            'tecnico_asignado'       => ['nullable','integer','exists:users,id'],
            'comision_habilitada'    => ['sometimes','boolean'],
            'tipo_comision'          => ['required_if:comision_habilitada,1', Rule::in(['porcentaje','valor_fijo']), 'nullable'],
            'valor_comision'         => ['required_if:comision_habilitada,1','nullable','numeric','min:0'],
            'estado'                 => ['sometimes', Rule::in(['pendiente','en_proceso','finalizado','cancelado'])],
            'observaciones'          => ['nullable','string'],
            'fecha_finalizacion'     => ['nullable','date'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->comision_habilitada) {
                $tipoComision = $this->tipo_comision;
                $valorComision = $this->valor_comision;
                $valorEstimado = $this->valor_estimado ?? 0;

                if ($tipoComision === 'valor_fijo' && $valorComision !== null && $valorComision > $valorEstimado) {
                    $validator->errors()->add(
                        'valor_comision',
                        'El valor de la comisión no puede ser mayor al valor estimado del equipo.'
                    );
                }

                if ($tipoComision === 'porcentaje' && $valorComision !== null) {
                    if ($valorComision < 0 || $valorComision > 100) {
                        $validator->errors()->add(
                            'valor_comision',
                            'El porcentaje de comisión debe estar entre 0 y 100.'
                        );
                    }
                }
            }
        });
    }
}