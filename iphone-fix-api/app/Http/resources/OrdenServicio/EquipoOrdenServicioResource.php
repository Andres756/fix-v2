<?php

namespace App\Http\Resources\OrdenServicio;

use Illuminate\Http\Resources\Json\JsonResource;

class EquipoOrdenServicioResource extends JsonResource
{
    public function toArray($request): array
    {
        // Calcular total (solo si ya vienen cargadas las relaciones)
        $totalTareas = $this->whenLoaded('tareas', function () {
            return $this->tareas->sum('costo_aplicado');
        }, 0);

        $totalRepuestos = $this->whenLoaded('repuestosInventario', function () {
            return $this->repuestosInventario->sum('costo_total');
        }, 0);

        $totalExternos = $this->whenLoaded('repuestosExternos', function () {
            return $this->repuestosExternos->sum('costo_total');
        }, 0);

        $precioTotal = $totalTareas + $totalRepuestos + $totalExternos;

        return [
            'id'                     => $this->id,
            'orden_id'               => $this->orden_id,

            'imei_serial'            => $this->imei_serial,
            'marca'                  => $this->marca,
            'modelo'                 => $this->modelo,
            'descripcion_problema'   => $this->descripcion_problema,
            'contrasena_equipo'      => $this->contrasena_equipo,
            'valor_estimado'         => $this->valor_estimado !== null ? (float)$this->valor_estimado : null,
            'fecha_estimada_entrega' => optional($this->fecha_estimada_entrega)->toDateString(),

            // Relación técnico
            'tecnico_asignado'       => $this->tecnico_asignado,
            'tecnico'                => $this->whenLoaded('tecnico', function () {
                return [
                    'id'     => $this->tecnico->id,
                    'nombre' => $this->tecnico->name ?? $this->tecnico->nombre ?? null,
                    'email'  => $this->tecnico->email ?? null,
                ];
            }),

            // Comisión
            'comision_habilitada'    => (bool)$this->comision_habilitada,
            'tipo_comision'          => $this->tipo_comision,
            'valor_comision'         => $this->valor_comision !== null ? (float)$this->valor_comision : null,

            // Estado (relación)
            'estado' => $this->estado ?? 'Pendiente',

            // Nuevos campos
            'precio_total'           => (float)$precioTotal,
            'facturado'              => (int)$this->facturado,
            'entregado'              => (int)$this->entregado,

            // Campos varios
            'observaciones'          => $this->observaciones,
            'fecha_finalizacion'     => $this->fecha_finalizacion
                                            ? $this->fecha_finalizacion->toDateTimeString()
                                            : null,

            'created_at'             => optional($this->created_at)->toDateTimeString(),
            'updated_at'             => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
