<?php
// app/Http/Controllers/Api/Parametros/TiposTrabajoController.php
namespace App\Http\Controllers\Api\Parametros\OrdenServicio;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parametros\TiposTrabajo\StoreTipoTrabajoRequest;
use App\Http\Requests\Parametros\TiposTrabajo\UpdateTipoTrabajoRequest;
use App\Http\Resources\Parametros\TipoTrabajoResource;
use App\Models\Parametros\TipoTrabajo;
use App\Services\Parametros\TiposTrabajoService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TiposTrabajoController extends Controller
{
    public function __construct(private TiposTrabajoService $service) {}

    public function index(Request $r)
    { return TipoTrabajoResource::collection($this->service->list($r->all())); }

    public function store(StoreTipoTrabajoRequest $r)
    { $x=$this->service->create($r->validated()); return (new TipoTrabajoResource($x))->response()->setStatusCode(Response::HTTP_CREATED); }

    public function show(TipoTrabajo $tipo_trabajo)
    { return new TipoTrabajoResource($tipo_trabajo); }

    public function update(UpdateTipoTrabajoRequest $r, TipoTrabajo $tipo_trabajo)
    { return new TipoTrabajoResource($this->service->update($tipo_trabajo,$r->validated())); }

    public function options()
    { return response()->json($this->service->options()); }
}
