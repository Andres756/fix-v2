<?php

namespace App\Services\Facturacion;

use App\Models\Facturacion\{
    Factura,
    FacturaDetalle,
    FacturaAuditoria,
    EstadoFactura
};
use App\Models\Inventario\{
    MovimientoInventario
};
use App\Models\OrdenServicio\{
    EquipoOrdenServicio
};
use Illuminate\Support\Facades\DB;

class AnulacionFacturaService
{
    /**
     * Procesa la anulación total o parcial de una factura.
     * Admite reglas específicas para repuestos y comisión de técnicos.
     */
    public function anularFacturaAvanzado(array $data, int $facturaId)
    {
        $factura = Factura::with(['detalles.estado'])->findOrFail($facturaId);
        $usuarioId = auth()->id();

        $estadoAnulado = EstadoFactura::where('codigo', 'ANUL')->firstOrFail();

        DB::beginTransaction();
        try {
            $detallesSeleccionados = $data['detalles'] ?? [];
            $acciones = $data['acciones'] ?? [];

            /**
             * 🔸 1️⃣ Si no se especifican detalles → anulación total
             */
            if (empty($detallesSeleccionados)) {
                foreach ($factura->detalles as $detalle) {
                    $detalle->update([
                        'entregado' => 0,
                        'estado_id' => $estadoAnulado->id,
                    ]);

                    $this->procesarReglasOrdenServicio($factura, $detalle, $acciones, $usuarioId);
                }

                // ✅ Actualizar factura
                $factura->update([
                    'estado_id' => $estadoAnulado->id,
                    'entregado' => 0,
                ]);

                // Auditoría
                $this->registrarAuditoria(
                    $factura->id,
                    $usuarioId,
                    'ANULAR',
                    'Factura anulada completamente (sin detalles específicos).'
                );

                DB::commit();
                return ['message' => 'Factura anulada completamente', 'tipo' => 'total'];
            }

            /**
             * 🔹 2️⃣ Anulación parcial (solo ítems seleccionados)
             */
            foreach ($factura->detalles as $detalle) {
                // Permite usar ID de detalle o ID de equipo (referencia_id)
                if (in_array($detalle->id, $detallesSeleccionados) || in_array($detalle->referencia_id, $detallesSeleccionados)) {
                    $detalle->update([
                        'entregado' => 0,
                        'estado_id' => $estadoAnulado->id,
                    ]);

                    $this->procesarReglasOrdenServicio($factura, $detalle, $acciones, $usuarioId);

                    // ✅ Auditoría individual por ítem
                    $this->registrarAuditoria(
                        $factura->id,
                        $usuarioId,
                        'ANULAR_PARCIAL',
                        "Se anuló el ítem #{$detalle->id} ({$detalle->descripcion})."
                    );
                }
            }

            /**
             * 🧩 3️⃣ Verificar si todos los ítems están anulados
             */
            $factura->load('detalles.estado');

            $activos = $factura->detalles
                ->filter(fn($d) => $d->estado?->codigo !== 'ANUL')
                ->count();

            if ($activos === 0) {
                // ✅ Todos los ítems anulados → anular factura completa
                $factura->update([
                    'estado_id' => $estadoAnulado->id,
                    'entregado' => 0,
                ]);

                $this->registrarAuditoria(
                    $factura->id,
                    $usuarioId,
                    'ANULAR',
                    'Todos los ítems anulados, factura marcada como ANULADA.'
                );
            } else {
                // ⚠️ Solo algunos ítems anulados → factura sigue activa
                $this->registrarAuditoria(
                    $factura->id,
                    $usuarioId,
                    'ANULAR_PARCIAL',
                    sprintf(
                        'Se anularon %d ítems. La factura sigue activa.',
                        count($detallesSeleccionados)
                    )
                );
            }

            DB::commit();

            return [
                'message' => $activos === 0
                    ? 'Factura anulada completamente'
                    : 'Anulación parcial registrada correctamente',
                'factura_id' => $factura->id,
                'tipo' => $activos === 0 ? 'total' : 'parcial',
            ];

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Aplica reglas específicas a los equipos de orden de servicio (repuestos y comisiones)
     */
    private function procesarReglasOrdenServicio(Factura $factura, FacturaDetalle $detalle, array $acciones, int $usuarioId): void
    {
        if ($detalle->tipo_item !== 'orden_servicio_equipo' || !$factura->orden_servicio_id) {
            return;
        }

        $equipoId = $detalle->referencia_id;
        $equipo = EquipoOrdenServicio::with([
            'repuestosInventario.inventario',
            'repuestosExternos',
            'tareas'
        ])->find($equipoId);

        if (!$equipo) {
            return;
        }

        /**
         * 🔹 REPUESTOS INTERNOS
         */
        if (!empty($acciones['repuestos_internos'])) {
            foreach ($equipo->repuestosInventario as $rep) {
                if ($acciones['repuestos_internos'] === 'reutilizables') {
                    // Reingresar stock
                    $rep->inventario->increment('stock', $rep->cantidad);

                    MovimientoInventario::create([
                        'inventario_id' => $rep->inventario_id,
                        'tipo_movimiento' => 'entrada',
                        'cantidad' => $rep->cantidad,
                        'stock_anterior' => $rep->inventario->stock - $rep->cantidad,
                        'stock_nuevo' => $rep->inventario->stock,
                        'motivo_id' => null,
                        'usuario_id' => $usuarioId,
                        'observaciones' => "Reversa por anulación de equipo #$equipoId (factura {$factura->codigo})",
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $rep->update(['estado' => $acciones['repuestos_internos']]);
            }
        }

        /**
         * 🔹 REPUESTOS EXTERNOS
         */
        if (!empty($acciones['repuestos_externos'])) {
            foreach ($equipo->repuestosExternos as $rep) {
                $rep->update(['estado' => $acciones['repuestos_externos']]);
            }
        }

        /**
         * 🔹 COMISIÓN DEL TÉCNICO
         */
        if (!empty($acciones['comision'])) {
            foreach ($equipo->tareas as $tarea) {
                if ($acciones['comision'] === 'descontar_pago') {
                    $tarea->update([
                        'comision_pagada' => 0,
                        'valor_pagado' => 0
                    ]);
                }
            }

            $this->registrarAuditoria(
                $factura->id,
                $usuarioId,
                'EDITAR',
                "Comisión del técnico marcada como: {$acciones['comision']}."
            );
        }
    }

    /**
     * Crea registros de auditoría centralizados
     */
    private function registrarAuditoria(int $facturaId, int $usuarioId, string $accion, string $detalle): void
    {
        FacturaAuditoria::create([
            'factura_id' => $facturaId,
            'usuario_id' => $usuarioId,
            'accion' => $accion,
            'detalle' => $detalle,
            'created_at' => now(),
        ]);
    }
}
