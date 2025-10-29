<?php

namespace App\Http\Controllers\Api\Facturacion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Facturacion\CrearFacturaRequest;
use App\Http\Resources\Facturacion\FacturaResource;
use App\Services\Facturacion\FacturacionService;
use Illuminate\Validation\ValidationException;
use App\Models\Facturacion\Factura;

class FacturacionController extends Controller
{
    protected FacturacionService $service;

    public function __construct(FacturacionService $service)
    {
        $this->service = $service;
    }

    /**
     * 📊 Listar facturas (vista resumen)
     */
    public function index()
    {
        $facturas = $this->service->listarResumen(request()->all());
        return response()->json($facturas);
    }

    /**
     * 🧾 Crear una nueva factura (venta o servicio)
     */
    public function store(CrearFacturaRequest $request)
    {
        $userId = (int) $request->user()->id;
        $data = $request->validated();

        try {
            if ($data['origen'] === 'venta') {
                $factura = $this->service->crearFacturaVenta($data, $userId);
            } elseif ($data['origen'] === 'servicio') {
                $factura = $this->service->crearFacturaServicio(
                    $data['orden_id'],
                    $data['cliente_id'],
                    $data['forma_pago_id'] ?? null,
                    $userId,
                    $data['observaciones'] ?? null
                );
            } else {
                throw ValidationException::withMessages(['origen' => 'Origen no soportado.']);
            }

            return new FacturaResource($factura->load(['detalles', 'pagos', 'auditorias', 'cliente', 'usuario', 'estado']));

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            // 🧩 Detectar error personalizado desde el trigger
            if (str_contains($e->getMessage(), 'STOCK_INSUFICIENTE|')) {
                // Extraer ID del producto desde el mensaje SQL
                $match = explode('|', $e->getMessage());
                $productoId = $match[1] ?? null;

                $inventario = \App\Models\Inventario\Inventario::find($productoId);

                $nombre = $inventario?->nombre ?? 'un producto del inventario';
                $stock = $inventario?->stock ?? 0;

                return response()->json([
                    'message' => 'No se puede procesar la factura.',
                    'error' => "El producto '{$nombre}' no cuenta con stock suficiente. Disponibles actualmente: {$stock} unidades.",
                ], 422);
            }

            // 🧩 Otros errores (por ejemplo, bloqueo plan separe, validaciones, etc.)
            return response()->json([
                'message' => 'Error al crear la factura',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * 📄 Mostrar factura detallada
     */
    public function show($id)
    {
        $factura = Factura::with(['cliente', 'usuario', 'formaPago', 'estado', 'detalles', 'pagos', 'auditorias'])
            ->findOrFail($id);

        return new FacturaResource($factura);
    }

    /**
     * ❌ Anular factura
     */
    public function anular($id)
    {
        $userId = (int) request()->user()->id;

        try {
            $factura = $this->service->anularFactura($id, $userId);
            return new FacturaResource($factura->load(['estado', 'cliente']));
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'No se pudo anular la factura',
                'error'   => $e->getMessage(),
            ], 422);
        }
    }
}
