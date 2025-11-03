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

    protected function procesarReglasOrdenServicio(Factura $factura, FacturaDetalle $detalle, array $acciones, int $usuarioId)
    {
        try {
            // Solo aplica si la factura pertenece a una orden de servicio
            if (!$factura->orden_servicio_id || $detalle->tipo_item !== 'orden_servicio_equipo') {
                return;
            }

            $equipoId = $detalle->referencia_id;
            $equipo = \App\Models\OrdenServicio\EquipoOrdenServicio::with([
                'repuestosInventario',
                'repuestosExternos',
                'tareas'
            ])->find($equipoId);

            if (!$equipo) {
                \Log::warning("❗ Equipo no encontrado durante anulación", ['equipo_id' => $equipoId]);
                return;
            }

            \Log::info('⚙️ Procesando reglas de anulación para equipo', [
                'factura_id' => $factura->id,
                'equipo_id' => $equipoId,
                'acciones' => $acciones
            ]);

            /**
             * 🧩 1️⃣ REPUESTOS INTERNOS (inventario)
             */
            if (!empty($equipo->repuestosInventario)) {
                foreach ($equipo->repuestosInventario as $rep) {
                    if (($acciones['repuestos_internos'] ?? null) === 'reutilizables') {
                        // 🔹 Devolver stock al inventario
                        $inv = $rep->inventario;
                        if ($inv) {
                            $inv->increment('stock', $rep->cantidad);
                            \Log::info('🔄 Repuesto interno devuelto al inventario', [
                                'inventario_id' => $inv->id,
                                'cantidad' => $rep->cantidad
                            ]);
                        }
                    } elseif (($acciones['repuestos_internos'] ?? null) === 'mantener') {
                        // 🔸 No hacer nada (quedan como usados)
                        \Log::info('🚫 Repuesto interno mantenido sin cambios', [
                            'repuesto_id' => $rep->id
                        ]);
                    }
                }
            }

            /**
             * 🧩 2️⃣ REPUESTOS EXTERNOS
             */
            if (!empty($equipo->repuestosExternos)) {
                foreach ($equipo->repuestosExternos as $repExt) {
                    if (($acciones['repuestos_externos'] ?? null) === 'reutilizables') {
                        // 🔹 Marcar como reutilizable
                        $repExt->update(['estado' => 'reutilizable']);
                        \Log::info('♻️ Repuesto externo marcado como reutilizable', [
                            'repuesto_externo_id' => $repExt->id
                        ]);
                    } elseif (($acciones['repuestos_externos'] ?? null) === 'mantener') {
                        // 🔸 No se toca (quedó consumido)
                        \Log::info('🧩 Repuesto externo mantenido (sin reversa)', [
                            'repuesto_externo_id' => $repExt->id
                        ]);
                    }
                }
            }

            /**
             * 🧩 3️⃣ COMISIÓN DEL TÉCNICO
             */
            if ($equipo->comision_habilitada) {
                if (($acciones['comision'] ?? null) === 'reversar_pago') {
                    // 🔹 Revertir comisión
                    $equipo->update(['valor_comision' => 0, 'comision_habilitada' => 0]);
                    \Log::info('💸 Comisión del técnico revertida', [
                        'equipo_id' => $equipo->id
                    ]);
                } elseif (($acciones['comision'] ?? null) === 'mantener_pago') {
                    // 🔸 Mantener comisión pagada
                    \Log::info('✅ Comisión del técnico mantenida sin cambios', [
                        'equipo_id' => $equipo->id
                    ]);
                }
            }

            /**
             * 🧩 4️⃣ Registrar auditoría de acciones sobre el equipo
             */
            \App\Models\Facturacion\FacturaAuditoria::create([
                'factura_id' => $factura->id,
                'usuario_id' => $usuarioId,
                'accion'     => 'ANULAR_PARCIAL',
                'detalle'    => sprintf(
                    'Se aplicaron reglas de anulación al equipo #%d: repuestos_internos=%s, repuestos_externos=%s, comision=%s',
                    $equipoId,
                    $acciones['repuestos_internos'] ?? 'N/A',
                    $acciones['repuestos_externos'] ?? 'N/A',
                    $acciones['comision'] ?? 'N/A'
                ),
                'created_at' => now(),
            ]);

        } catch (\Throwable $e) {
            \Log::error('❌ Error procesando reglas de orden de servicio en anulación', [
                'error' => $e->getMessage(),
                'factura_id' => $factura->id,
                'detalle_id' => $detalle->id ?? null
            ]);
        }
    }
}
