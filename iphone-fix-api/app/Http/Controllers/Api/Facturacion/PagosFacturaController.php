<?php

namespace App\Http\Controllers\Api\Facturacion;

use App\Http\Controllers\Controller;
use App\Models\Facturacion\Factura;
use App\Models\Facturacion\PagoFactura;
use App\Models\Facturacion\FacturaAuditoria;
use App\Models\Facturacion\MotivoAnulacionPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PagosFacturaController extends Controller
{

    /**
     * 📋 Listar pagos de una factura
     */
    public function index($facturaId)
    {
    $pagos = PagoFactura::with(['formaPago', 'usuario', 'motivoAnulacion'])
        ->where('factura_id', $facturaId)
        ->orderBy('id', 'desc')
        ->get()
        ->map(function ($pago) {
            return [
                'id' => $pago->id,
                'fecha' => optional($pago->created_at)->format('Y-m-d H:i'),
                'valor' => (float) $pago->valor,
                'forma_pago' => $pago->formaPago?->nombre ?? '—',
                'usuario' => $pago->usuario?->name ?? '—',
                'estado' => $pago->estado,
                'motivo_anulacion' => $pago->motivoAnulacion?->nombre ?? null,
                'observaciones' => $pago->observaciones,
            ];
        });

        return response()->json([
            'message' => 'Pagos de la factura obtenidos correctamente',
            'data' => $pagos,
        ]);
    }

    /**
     * 💳 Registrar uno o varios pagos a una factura existente.
     */
    public function store(int $facturaId, Request $request)
    {
        $request->validate([
            'pagos' => 'required|array|min:1',
            'pagos.*.forma_pago_id' => 'required|integer|exists:formas_pago,id',
            'pagos.*.valor' => 'required|numeric|min:0.01',
            'pagos.*.observaciones' => 'nullable|string|max:255',
            'pagos.*.referencia_externa' => 'nullable|string|max:255',
            'monto_recibido' => 'nullable|numeric|min:0',
        ]);

        $usuarioId = Auth::id() ?? $request->input('usuario_id');
        $factura = Factura::with(['pagos', 'estado'])->findOrFail($facturaId);

        // 🚫 Bloquear pagos sobre facturas anuladas o ya pagadas
        if (in_array($factura->estado?->codigo, ['ANUL', 'PAGA'])) {
            return response()->json([
                'message' => 'No se pueden registrar más pagos sobre una factura anulada o ya pagada.'
            ], 422);
        }

        $saldoPendiente = $factura->saldo_pendiente;
        $totalPagadoAhora = 0;
        $vueltas = 0;

        foreach ($request->pagos as $pago) {
            $valorPago = (float)$pago['valor'];

            // 💡 Si intenta pagar más del saldo pendiente, calcular vueltas
            if ($valorPago > $saldoPendiente) {
                $vueltas = $valorPago - $saldoPendiente;
                $valorPago = $saldoPendiente;
            }

            if ($valorPago > 0) {
                // 💾 Crear el registro en pagos_factura
                $nuevoPago = PagoFactura::create([
                    'factura_id'        => $factura->id,
                    'forma_pago_id'     => $pago['forma_pago_id'],
                    'valor'             => $valorPago,
                    'referencia_externa'=> $pago['referencia_externa'] ?? null,
                    'observaciones'     => $pago['observaciones'] ?? null,
                    'usuario_id'        => $usuarioId,
                ]);

                // 🧾 Registrar la acción en factura_auditoria
                FacturaAuditoria::create([
                    'factura_id' => $factura->id,
                    'usuario_id' => $usuarioId,
                    'accion'     => 'PAGAR',
                    'detalle'    => sprintf(
                        'Abono registrado por usuario #%s. Valor: $%s.',
                        $usuarioId,
                        number_format($valorPago, 0, ',', '.')
                    ),
                ]);

                $saldoPendiente -= $valorPago;
                $totalPagadoAhora += $valorPago;

                if ($saldoPendiente <= 0) {
                    $saldoPendiente = 0;
                    break;
                }
            }
        }

        // 🔄 Recargar la factura con datos actualizados
        $factura->refresh()->load('pagos', 'estado');

        return response()->json([
            'message' => 'Pagos registrados correctamente',
            'factura' => $factura,
            'total_pagado' => $factura->total_pagado,
            'saldo_pendiente' => $saldoPendiente,
            'vueltas' => $vueltas,
        ]);
    }

    public function motivosAnulacion()
    {
        $motivos = MotivoAnulacionPago::where('activo', 1)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return response()->json([
            'message' => 'Motivos de anulación obtenidos correctamente',
            'data' => $motivos
        ]);
    }

    /**
     * ❌ Anular un pago de factura
     */
    public function anular(Request $request, $id)
    {
        try {
            $request->validate([
                'motivo_anulacion_id' => 'required|exists:motivos_anulacion_pagos,id',
            ]);

            // 🔹 Buscar el pago junto con su factura
            $pago = \App\Models\Facturacion\PagoFactura::with('factura')->findOrFail($id);

            if ($pago->estado === 'anulado') {
                return response()->json([
                    'message' => 'El pago ya se encuentra anulado.',
                ], 400);
            }

            // 🔹 1. Anular el pago
            $pago->update([
                'estado' => 'anulado',
                'motivo_anulacion_id' => $request->motivo_anulacion_id,
                'anulado_por_id' => auth()->id(),
                'anulado_at' => now(),
            ]);

            // 🔹 2. Obtener la factura asociada
            $factura = $pago->factura;
            if (!$factura) {
                return response()->json([
                    'message' => 'No se encontró la factura asociada a este pago.',
                ], 404);
            }

            // 🔹 3. Calcular pagos activos (no anulados)
            $totalPagado = $factura->pagos()
                ->where('estado', '!=', 'anulado')
                ->sum('valor');

            // 🔹 4. Calcular saldo pendiente
            $saldoPendiente = max($factura->total - $totalPagado, 0);

            // 🔹 5. Cambiar estado a "Pendiente" (ID = 1)
            $factura->estado_id = 1;
            $factura->save();

            // 🔹 6. Devolver respuesta
            return response()->json([
                'message' => 'Pago anulado correctamente',
                'data' => [
                    'pago' => [
                        'id' => $pago->id,
                        'estado' => $pago->estado,
                        'motivo_anulacion' => $pago->motivoAnulacion?->nombre,
                    ],
                    'factura' => [
                        'id' => $factura->id,
                        'estado_id' => $factura->estado_id,
                        'estado' => $factura->estado->nombre ?? 'Pendiente',
                        'total' => $factura->total,
                        'total_pagado' => $totalPagado,
                        'saldo_pendiente' => $saldoPendiente,
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al anular el pago',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


}
