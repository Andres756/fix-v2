<?php
// app/Http/Controllers/Api/Parametros/TiposDeInventarioController.php
namespace App\Http\Controllers\Api\Parametros\Inventario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parametros\TiposDeInventario\StoreTipoDeInventarioRequest;
use App\Http\Requests\Parametros\TiposDeInventario\UpdateTipoDeInventarioRequest;
use App\Http\Resources\Parametros\TipoDeInventarioResource;
use App\Models\Parametros\TipoDeInventario;
use App\Services\Parametros\TiposDeInventarioService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TiposDeInventarioController extends Controller
{
    public function __construct(private TiposDeInventarioService $service) {}

    public function index(Request $r)
    { return TipoDeInventarioResource::collection($this->service->list($r->all())); }

    public function store(StoreTipoDeInventarioRequest $r)
    { $x=$this->service->create($r->validated()); return (new TipoDeInventarioResource($x))->response()->setStatusCode(Response::HTTP_CREATED); }

    public function show(TipoDeInventario $tipo_de_inventario)
    { return new TipoDeInventarioResource($tipo_de_inventario); }

    public function update(UpdateTipoDeInventarioRequest $r, TipoDeInventario $tipo_de_inventario)
    { return new TipoDeInventarioResource($this->service->update($tipo_de_inventario,$r->validated())); }

    public function options()
    { return response()->json($this->service->options()); }
}
