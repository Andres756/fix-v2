<?php
// app/Http/Controllers/Api/Ventas/FacturasLogController.php
namespace App\Http\Controllers\Api\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Ventas\FacturaLog;
use App\Http\Resources\Ventas\FacturaLogResource;
use App\Http\Requests\Ventas\Facturas\StoreFacturaLogRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FacturasLogController extends Controller
{
    public function index(Request $r){
        $per = min($r->integer('per_page',15),100);
        $q = FacturaLog::query();
        if ($factura = $r->query('factura_id')) $q->where('factura_id',$factura);
        return FacturaLogResource::collection($q->paginate($per));
    }
    public function store(StoreFacturaLogRequest $r){
        $x = FacturaLog::create($r->validated());
        return (new FacturaLogResource($x))->response()->setStatusCode(Response::HTTP_CREATED);
    }
    public function show(FacturaLog $facturas_log){
        return new FacturaLogResource($facturas_log);
    }
    public function destroy(FacturaLog $facturas_log){
        $facturas_log->delete();
        return response()->noContent();
    }
}
