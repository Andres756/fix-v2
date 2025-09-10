<?php

namespace App\Http\Controllers\Api\Parametros\Facturacion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parametros\TiposVenta\StoreTipoVentaRequest;
use App\Http\Requests\Parametros\TiposVenta\UpdateTipoVentaRequest;
use App\Http\Resources\Parametros\TipoVentaResource;
use App\Models\Parametros\TipoVenta;
use App\Services\Parametros\TiposVentaService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TiposVentaController extends Controller
{
    public function __construct(private TiposVentaService $service)
    {
        // sin middleware mientras pruebas
    }

    public function index(Request $request)
    {
        $items = $this->service->list($request->all());
        return TipoVentaResource::collection($items);
    }

    public function store(StoreTipoVentaRequest $request)
    {
        $tv = $this->service->create($request->validated());
        return (new TipoVentaResource($tv))
            ->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(TipoVenta $tipo_venta)
    {
        return new TipoVentaResource($tipo_venta);
    }

    public function update(UpdateTipoVentaRequest $request, TipoVenta $tipo_venta)
    {
        $tv = $this->service->update($tipo_venta, $request->validated());
        return new TipoVentaResource($tv);
    }

    public function toggle(int $id)
    {
        $tv = $this->service->toggle($id);
        return new TipoVentaResource($tv);
    }

    public function destroy(int $id)
    {
        $this->service->deactivate($id);
        return response()->noContent();
    }

    public function options(Request $request)
    {
        $soloActivos = filter_var($request->query('solo_activos', 'true'), FILTER_VALIDATE_BOOLEAN);
        return response()->json($this->service->options($soloActivos));
    }
}
