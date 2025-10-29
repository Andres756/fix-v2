<?php

namespace App\Http\Controllers\Api\Parametros\Facturacion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parametros\FormasPago\StoreFormaPagoRequest;
use App\Http\Requests\Parametros\FormasPago\UpdateFormaPagoRequest;
use App\Http\Resources\Parametros\FormaPagoResource;
use App\Models\Parametros\FormaPago;
use App\Services\Parametros\FormasPagoService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FormasPagoController extends Controller
{
    public function __construct(private FormasPagoService $service)
    {
        //$this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        $items = $this->service->list($request->all());
        return FormaPagoResource::collection($items);
    }

    public function store(StoreFormaPagoRequest $request)
    {
        $fp = $this->service->create($request->validated());
        return (new FormaPagoResource($fp))
            ->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(FormaPago $forma_pago)
    {
        return new FormaPagoResource($forma_pago);
    }

    public function update(UpdateFormaPagoRequest $request, FormaPago $forma_pago)
    {
        $fp = $this->service->update($forma_pago, $request->validated());
        return new FormaPagoResource($fp);
    }

    // Activar/Desactivar con switch
    public function toggle(int $id)
    {
        $fp = $this->service->toggle($id);
        return new FormaPagoResource($fp);
    }

    // “Eliminar” lógico (opcional)
    public function destroy(int $id)
    {
        $this->service->deactivate($id);
        return response()->noContent();
    }

    // Para combos/selects
    public function options()
    {
        $data = \App\Models\Parametros\FormaPago::select('id', 'nombre')
            ->where('activo', 1) // o 'activo', según tu campo
            ->orderBy('nombre')
            ->get();

        return response()->json($data);
    }
}
