<?php
// app/Http/Controllers/Api/Parametros/EstadosPlanSepareController.php
namespace App\Http\Controllers\Api\Parametros\PlanSepare;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parametros\EstadosPlanSepare\StoreEstadoPlanSepareRequest;
use App\Http\Requests\Parametros\EstadosPlanSepare\UpdateEstadoPlanSepareRequest;
use App\Http\Resources\Parametros\EstadoPlanSepareResource;
use App\Models\Parametros\EstadoPlanSepare;
use App\Services\Parametros\EstadosPlanSepareService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EstadosPlanSepareController extends Controller
{
    public function __construct(private EstadosPlanSepareService $service) {}

    public function index(Request $r)
    { return EstadoPlanSepareResource::collection($this->service->list($r->all())); }

    public function store(StoreEstadoPlanSepareRequest $r)
    { $x=$this->service->create($r->validated()); return (new EstadoPlanSepareResource($x))->response()->setStatusCode(Response::HTTP_CREATED); }

    public function show(EstadoPlanSepare $estado_plan_separe)
    { return new EstadoPlanSepareResource($estado_plan_separe); }

    public function update(UpdateEstadoPlanSepareRequest $r, EstadoPlanSepare $estado_plan_separe)
    { return new EstadoPlanSepareResource($this->service->update($estado_plan_separe,$r->validated())); }

    public function toggle(int $id)
    { return new EstadoPlanSepareResource($this->service->toggle($id)); }

    public function destroy(int $id)
    { $this->service->deactivate($id); return response()->noContent(); }

    public function options(Request $r)
    { $solo = filter_var($r->query('solo_activos','true'), FILTER_VALIDATE_BOOLEAN); return response()->json($this->service->options($solo)); }
}
