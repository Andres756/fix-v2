<?php

namespace App\Http\Controllers\Api\Parametros;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parametros\TiposDescuento\StoreTipoDescuentoRequest;
use App\Http\Requests\Parametros\TiposDescuento\UpdateTipoDescuentoRequest;
use App\Http\Resources\Parametros\TipoDescuentoResource;
use App\Models\Parametros\TipoDescuento;
use App\Services\Parametros\TiposDescuentoService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TiposDescuentoController extends Controller
{
    public function __construct(private TiposDescuentoService $service) {}

    public function index(Request $r){ return TipoDescuentoResource::collection($this->service->list($r->all())); }
    public function store(StoreTipoDescuentoRequest $r){ $x=$this->service->create($r->validated()); return (new TipoDescuentoResource($x))->response()->setStatusCode(Response::HTTP_CREATED); }
    public function show(TipoDescuento $tipo_descuento){ return new TipoDescuentoResource($tipo_descuento); }
    public function update(UpdateTipoDescuentoRequest $r, TipoDescuento $tipo_descuento){ return new TipoDescuentoResource($this->service->update($tipo_descuento,$r->validated())); }
    public function toggle(int $id){ return new TipoDescuentoResource($this->service->toggle($id)); }
    public function destroy(int $id){ $this->service->deactivate($id); return response()->noContent(); }
    public function options(Request $r){ $solo = filter_var($r->query('solo_activos','true'), FILTER_VALIDATE_BOOLEAN); return response()->json($this->service->options($solo)); }
}
