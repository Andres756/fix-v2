<?php
// app/Http/Controllers/Api/Parametros/MotivosIngresoController.php
namespace App\Http\Controllers\Api\Parametros\Inventario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parametros\MotivosIngreso\StoreMotivoIngresoRequest;
use App\Http\Requests\Parametros\MotivosIngreso\UpdateMotivoIngresoRequest;
use App\Http\Resources\Parametros\MotivoIngresoResource;
use App\Models\Parametros\MotivoIngreso;
use App\Services\Parametros\MotivosIngresoService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MotivosIngresoController extends Controller
{
    public function __construct(private MotivosIngresoService $service) {}

    public function index(Request $r)
    { return MotivoIngresoResource::collection($this->service->list($r->all())); }

    public function store(StoreMotivoIngresoRequest $r)
    { $x=$this->service->create($r->validated()); return (new MotivoIngresoResource($x))->response()->setStatusCode(Response::HTTP_CREATED); }

    public function show(MotivoIngreso $motivo_ingreso)
    { return new MotivoIngresoResource($motivo_ingreso); }

    public function update(UpdateMotivoIngresoRequest $r, MotivoIngreso $motivo_ingreso)
    { return new MotivoIngresoResource($this->service->update($motivo_ingreso,$r->validated())); }

    public function options()
    { return response()->json($this->service->options()); }
}
