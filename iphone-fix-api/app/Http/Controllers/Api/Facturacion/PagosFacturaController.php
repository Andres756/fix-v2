<?php

namespace App\Http\Controllers\Api\Facturacion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Facturacion\RegistrarPagoRequest;
use App\Http\Resources\Facturacion\PagoFacturaResource;
use App\Services\Facturacion\FacturacionService;
use App\Models\Facturacion\PagoFactura;
use Illuminate\Validation\ValidationException;

class PagosFacturaController extends Controller
{
    protected FacturacionService $service;

    public function __construct(FacturacionService $service)
    {
        $this->service = $service;
    }

    /**
     * 💵 Registrar un pago o abono
     */
    public function store($id, RegistrarPagoRequest $request)
    {
        $userId = (int) $request->user()->id;
        $data = $request->validated();

        try {
            $pago = $this->service->registrarPagoFactura($id, $data, $userId);
            return new PagoFacturaResource($pago->load(['formaPago', 'usuario']));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'No se pudo registrar el pago',
                'error'   => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * 📋 Listar pagos de una factura
     */
    public function index($facturaId)
    {
        $pagos = PagoFactura::where('factura_id', $facturaId)
            ->with(['formaPago', 'usuario'])
            ->orderByDesc('id')
            ->get();

        return PagoFacturaResource::collection($pagos);
    }
}
