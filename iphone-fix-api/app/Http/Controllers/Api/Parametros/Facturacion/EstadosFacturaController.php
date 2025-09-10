<?php
// app/Http/Controllers/Api/Parametros/EstadosFacturaController.php
namespace App\Http\Controllers\Api\Parametros\Facturacion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parametros\EstadosFactura\StoreEstadoFacturaRequest;
use App\Http\Requests\Parametros\EstadosFactura\UpdateEstadoFacturaRequest;
use App\Http\Resources\Parametros\EstadoFacturaResource;
use App\Models\Parametros\EstadoFactura;
use App\Services\Parametros\EstadosFacturaService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EstadosFacturaController extends Controller
{
    public function __construct(private EstadosFacturaService $service) {}

    public function index(Request $r)
    { return EstadoFacturaResource::collection($this->service->list($r->all())); }

    public function store(StoreEstadoFacturaRequest $r)
    { $x=$this->service->create($r->validated()); return (new EstadoFacturaResource($x))->response()->setStatusCode(Response::HTTP_CREATED); }

    public function show(EstadoFactura $estado_factura)
    { return new EstadoFacturaResource($estado_factura); }

    public function update(UpdateEstadoFacturaRequest $r, EstadoFactura $estado_factura)
    { return new EstadoFacturaResource($this->service->update($estado_factura,$r->validated())); }

    public function toggle(int $id)
    { return new EstadoFacturaResource($this->service->toggle($id)); }

    public function destroy(int $id)
    { $this->service->deactivate($id); return response()->noContent(); }

    public function options(Request $r)
    { $solo = filter_var($r->query('solo_activos','true'), FILTER_VALIDATE_BOOLEAN); return response()->json($this->service->options($solo)); }
}
