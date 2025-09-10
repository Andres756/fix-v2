<?php
// app/Http/Controllers/Api/Parametros/TiposCambioPlanSepareController.php
namespace App\Http\Controllers\Api\Parametros\PlanSepare;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parametros\TiposCambioPlanSepare\StoreTipoCambioPlanSepareRequest;
use App\Http\Requests\Parametros\TiposCambioPlanSepare\UpdateTipoCambioPlanSepareRequest;
use App\Http\Resources\Parametros\TipoCambioPlanSepareResource;
use App\Models\Parametros\TipoCambioPlanSepare;
use App\Services\Parametros\TiposCambioPlanSepareService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TiposCambioPlanSepareController extends Controller
{
    public function __construct(private TiposCambioPlanSepareService $service) {}

    public function index(Request $r)
    { return TipoCambioPlanSepareResource::collection($this->service->list($r->all())); }

    public function store(StoreTipoCambioPlanSepareRequest $r)
    { $x=$this->service->create($r->validated()); return (new TipoCambioPlanSepareResource($x))->response()->setStatusCode(Response::HTTP_CREATED); }

    public function show(TipoCambioPlanSepare $tipo_cambio_plan_separe)
    { return new TipoCambioPlanSepareResource($tipo_cambio_plan_separe); }

    public function update(UpdateTipoCambioPlanSepareRequest $r, TipoCambioPlanSepare $tipo_cambio_plan_separe)
    { return new TipoCambioPlanSepareResource($this->service->update($tipo_cambio_plan_separe,$r->validated())); }

    public function toggle(int $id)
    { return new TipoCambioPlanSepareResource($this->service->toggle($id)); }

    public function destroy(int $id)
    { $this->service->deactivate($id); return response()->noContent(); }

    public function options(Request $r)
    { $solo = filter_var($r->query('solo_activos','true'), FILTER_VALIDATE_BOOLEAN); return response()->json($this->service->options($solo)); }
}
