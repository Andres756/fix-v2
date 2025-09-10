<?php
// app/Http/Controllers/Api/PlanSepare/PlanesSepareLogController.php
namespace App\Http\Controllers\Api\PlanSepare;

use App\Http\Controllers\Controller;
use App\Models\PlanSepare\PlanSepareLog;
use App\Http\Resources\PlanSepare\PlanSepareLogResource;
use App\Http\Requests\PlanSepare\StorePlanSepareLogRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PlanesSepareLogController extends Controller
{
    public function index(Request $r){
        $per = min($r->integer('per_page',15),100);
        $q = PlanSepareLog::query();
        if ($plan = $r->query('plan_id')) $q->where('plan_id',$plan);
        return PlanSepareLogResource::collection($q->paginate($per));
    }
    public function store(StorePlanSepareLogRequest $r){
        $x = PlanSepareLog::create($r->validated());
        return (new PlanSepareLogResource($x))->response()->setStatusCode(Response::HTTP_CREATED);
    }
    public function show(PlanSepareLog $plan_separe_log){
        return new PlanSepareLogResource($plan_separe_log);
    }
    public function destroy(PlanSepareLog $plan_separe_log){
        $plan_separe_log->delete();
        return response()->noContent();
    }
}
