<?php

namespace App\Http\Controllers\Api\Facturacion;

use App\Http\Controllers\Controller;
use App\Models\Facturacion\Factura;
use App\Models\Facturacion\PagoFactura;
use App\Models\Facturacion\FacturaAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PagosFacturaController extends Controller
{
    /**
     * 💳 Registrar uno o varios pagos a una factura existente.
     */
    public function store(Request $request, int $facturaId)
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
}
