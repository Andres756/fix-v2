<?php
// app/Http/Controllers/Api/PlanSepare/PlanesSepareController.php
namespace App\Http\Controllers\Api\PlanSepare;

use App\Http\Controllers\Controller;
use App\Models\PlanSepare\PlanSepare;
use App\Http\Resources\PlanSepare\PlanSepareResource;
use App\Http\Requests\PlanSepare\StorePlanSepareRequest;
use App\Http\Requests\PlanSepare\UpdatePlanSepareRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PlanesSepareController extends Controller
{
    public function index(Request $r){
        $per = min($r->integer('per_page',15),100);
        $q = PlanSepare::with(['abonos','logs']);
        if ($cli = $r->query('cliente_id')) $q->where('cliente_id',$cli);
        if ($estado = $r->query('estado_id')) $q->where('estado_id',$estado);
        return PlanSepareResource::collection($q->paginate($per));
    }
    public function store(StorePlanSepareRequest $r){
        $x = PlanSepare::create($r->validated());
        return (new PlanSepareResource($x->load(['abonos','logs'])))->response()->setStatusCode(Response::HTTP_CREATED);
    }
    public function show(PlanSepare $plan_separe){ return new PlanSepareResource($plan_separe->load(['abonos','logs'])); }
    public function update(UpdatePlanSepareRequest $r, PlanSepare $plan_separe){
        $plan_separe->update($r->validated());
        return new PlanSepareResource($plan_separe->load(['abonos','logs']));
    }
    public function destroy(PlanSepare $plan_separe){
        $plan_separe->delete();
        return response()->noContent();
    }
}
