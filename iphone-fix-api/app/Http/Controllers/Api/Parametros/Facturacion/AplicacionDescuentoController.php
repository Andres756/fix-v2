<?php
// app/Http/Controllers/Api/Parametros/AplicacionDescuentoController.php
namespace App\Http\Controllers\Api\Parametros\Facturacion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parametros\AplicacionDescuento\StoreAplicacionDescuentoRequest;
use App\Http\Requests\Parametros\AplicacionDescuento\UpdateAplicacionDescuentoRequest;
use App\Http\Resources\Parametros\AplicacionDescuentoResource;
use App\Models\Parametros\AplicacionDescuento;
use App\Services\Parametros\AplicacionDescuentoService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AplicacionDescuentoController extends Controller
{
    public function __construct(private AplicacionDescuentoService $service) {}

    public function index(Request $r)
    { return AplicacionDescuentoResource::collection($this->service->list($r->all())); }

    public function store(StoreAplicacionDescuentoRequest $r)
    { $x=$this->service->create($r->validated()); return (new AplicacionDescuentoResource($x))->response()->setStatusCode(Response::HTTP_CREATED); }

    public function show(AplicacionDescuento $aplicacion_descuento)
    { return new AplicacionDescuentoResource($aplicacion_descuento); }

    public function update(UpdateAplicacionDescuentoRequest $r, AplicacionDescuento $aplicacion_descuento)
    { return new AplicacionDescuentoResource($this->service->update($aplicacion_descuento,$r->validated())); }

    public function toggle(int $id)
    { return new AplicacionDescuentoResource($this->service->toggle($id)); }

    public function destroy(int $id)
    { $this->service->deactivate($id); return response()->noContent(); }

    public function options(Request $r)
    { $solo = filter_var($r->query('solo_activos','true'), FILTER_VALIDATE_BOOLEAN); return response()->json($this->service->options($solo)); }
}
