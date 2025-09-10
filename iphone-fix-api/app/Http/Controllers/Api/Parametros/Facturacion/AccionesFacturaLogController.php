<?php
// app/Http/Controllers/Api/Parametros/AccionesFacturaLogController.php
namespace App\Http\Controllers\Api\Parametros\Facturacion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parametros\AccionesFacturaLog\StoreAccionFacturaLogRequest;
use App\Http\Requests\Parametros\AccionesFacturaLog\UpdateAccionFacturaLogRequest;
use App\Http\Resources\Parametros\AccionFacturaLogResource;
use App\Models\Parametros\AccionFacturaLog;
use App\Services\Parametros\AccionesFacturaLogService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccionesFacturaLogController extends Controller
{
    public function __construct(private AccionesFacturaLogService $service) {}

    public function index(Request $r)
    { return AccionFacturaLogResource::collection($this->service->list($r->all())); }

    public function store(StoreAccionFacturaLogRequest $r)
    { $x=$this->service->create($r->validated()); return (new AccionFacturaLogResource($x))->response()->setStatusCode(Response::HTTP_CREATED); }

    public function show(AccionFacturaLog $accion_factura_log)
    { return new AccionFacturaLogResource($accion_factura_log); }

    public function update(UpdateAccionFacturaLogRequest $r, AccionFacturaLog $accion_factura_log)
    { return new AccionFacturaLogResource($this->service->update($accion_factura_log,$r->validated())); }

    public function toggle(int $id)
    { return new AccionFacturaLogResource($this->service->toggle($id)); }

    public function destroy(int $id)
    { $this->service->deactivate($id); return response()->noContent(); }

    public function options(Request $r)
    { $solo = filter_var($r->query('solo_activos','true'), FILTER_VALIDATE_BOOLEAN); return response()->json($this->service->options($solo)); }
}
