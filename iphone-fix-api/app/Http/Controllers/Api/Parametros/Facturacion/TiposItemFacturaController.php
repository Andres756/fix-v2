<?php
// app/Http/Controllers/Api/Parametros/TiposItemFacturaController.php
namespace App\Http\Controllers\Api\Parametros\Facturacion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parametros\TiposItemFactura\StoreTipoItemFacturaRequest;
use App\Http\Requests\Parametros\TiposItemFactura\UpdateTipoItemFacturaRequest;
use App\Http\Resources\Parametros\TipoItemFacturaResource;
use App\Models\Parametros\TipoItemFactura;
use App\Services\Parametros\TiposItemFacturaService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TiposItemFacturaController extends Controller
{
    public function __construct(private TiposItemFacturaService $service) {}

    public function index(Request $r)
    { return TipoItemFacturaResource::collection($this->service->list($r->all())); }

    public function store(StoreTipoItemFacturaRequest $r)
    { $x=$this->service->create($r->validated()); return (new TipoItemFacturaResource($x))->response()->setStatusCode(Response::HTTP_CREATED); }

    public function show(TipoItemFactura $tipo_item_factura)
    { return new TipoItemFacturaResource($tipo_item_factura); }

    public function update(UpdateTipoItemFacturaRequest $r, TipoItemFactura $tipo_item_factura)
    { return new TipoItemFacturaResource($this->service->update($tipo_item_factura,$r->validated())); }

    public function toggle(int $id)
    { return new TipoItemFacturaResource($this->service->toggle($id)); }

    public function destroy(int $id)
    { $this->service->deactivate($id); return response()->noContent(); }

    public function options(Request $r)
    { $solo = filter_var($r->query('solo_activos','true'), FILTER_VALIDATE_BOOLEAN); return response()->json($this->service->options($solo)); }
}
