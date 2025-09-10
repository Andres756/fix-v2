<?php
// app/Http/Controllers/Api/PlanSepare/AbonosPlanSepareController.php
namespace App\Http\Controllers\Api\PlanSepare;

use App\Http\Controllers\Controller;
use App\Models\PlanSepare\AbonoPlanSepare;
use App\Http\Resources\PlanSepare\AbonoPlanSepareResource;
use App\Http\Requests\PlanSepare\StoreAbonoPlanSepareRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AbonosPlanSepareController extends Controller
{
    public function index(Request $r){
        $per = min($r->integer('per_page',15),100);
        $q = AbonoPlanSepare::query();
        if ($plan = $r->query('plan_id')) $q->where('plan_id',$plan);
        return AbonoPlanSepareResource::collection($q->paginate($per));
    }
    public function store(StoreAbonoPlanSepareRequest $r){
        $x = AbonoPlanSepare::create($r->validated());
        return (new AbonoPlanSepareResource($x))->response()->setStatusCode(Response::HTTP_CREATED);
    }
    public function show(AbonoPlanSepare $abonos_plan_separe){
        return new AbonoPlanSepareResource($abonos_plan_separe);
    }
    public function destroy(AbonoPlanSepare $abonos_plan_separe){
        $abonos_plan_separe->delete();
        return response()->noContent();
    }
}
