<?php
// app/Http/Controllers/Api/Parametros/EstadosOrdenServicioController.php
namespace App\Http\Controllers\Api\Parametros\OrdenServicio;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parametros\EstadosOrdenServicio\StoreEstadoOrdenServicioRequest;
use App\Http\Requests\Parametros\EstadosOrdenServicio\UpdateEstadoOrdenServicioRequest;
use App\Http\Resources\Parametros\EstadoOrdenServicioResource;
use App\Models\Parametros\EstadoOrdenServicio;
use App\Services\Parametros\EstadosOrdenServicioService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EstadosOrdenServicioController extends Controller
{
    public function __construct(private EstadosOrdenServicioService $service) {}

    public function index(Request $r)
    { return EstadoOrdenServicioResource::collection($this->service->list($r->all())); }

    public function store(StoreEstadoOrdenServicioRequest $r)
    { $x=$this->service->create($r->validated()); return (new EstadoOrdenServicioResource($x))->response()->setStatusCode(Response::HTTP_CREATED); }

    public function show(EstadoOrdenServicio $estado_orden_servicio)
    { return new EstadoOrdenServicioResource($estado_orden_servicio); }

    public function update(UpdateEstadoOrdenServicioRequest $r, EstadoOrdenServicio $estado_orden_servicio)
    { return new EstadoOrdenServicioResource($this->service->update($estado_orden_servicio,$r->validated())); }

    public function options()
    { return response()->json($this->service->options()); }
}
