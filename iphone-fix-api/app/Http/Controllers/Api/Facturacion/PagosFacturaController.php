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
     * 📋 Listar pagos de una factura
     */
    public function index(int $facturaId)
    {
        $factura = Factura::with(['pagos.formaPago', 'estado'])->find($facturaId);

        if (!$factura) {
            return response()->json([
                'message' => 'Factura no encontrada.'
            ], 404);
        }

        return response()->json([
            'factura_id' => $factura->id,
            'codigo' => $factura->codigo,
            'estado' => $factura->estado?->codigo ?? 'DESCONOCIDO',
            'total' => $factura->total ?? 0,
            'total_pagado' => $factura->total_pagado ?? $factura->pagos->sum('valor'),
            'saldo_pendiente' => $factura->saldo_pendiente ?? max(0, ($factura->total ?? 0) - $factura->pagos->sum('valor')),
            'pagos' => $factura->pagos->map(function ($pago) {
                return [
                    'id' => $pago->id,
                    'forma_pago' => $pago->formaPago->nombre ?? null,
                    'valor' => $pago->valor,
                    'referencia_externa' => $pago->referencia_externa,
                    'observaciones' => $pago->observaciones,
                    'fecha' => $pago->created_at?->format('Y-m-d H:i:s'),
                    'usuario_id' => $pago->usuario_id,
                ];
            }),
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
}
