<?php

namespace App\Http\Controllers\Api\Parametros\Inventario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parametros\EstadosInventario\StoreEstadoInventarioRequest;
use App\Http\Requests\Parametros\EstadosInventario\UpdateEstadoInventarioRequest;
use App\Http\Resources\Parametros\EstadoInventarioResource;
use App\Models\Parametros\EstadoInventario;
use App\Services\Parametros\EstadosInventarioService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EstadosInventarioController extends Controller
{
    public function __construct(private EstadosInventarioService $service) {}

    public function index(Request $r){ return EstadoInventarioResource::collection($this->service->list($r->all())); }
    public function store(StoreEstadoInventarioRequest $r){ $x=$this->service->create($r->validated()); return (new EstadoInventarioResource($x))->response()->setStatusCode(Response::HTTP_CREATED); }
    public function show(EstadoInventario $estado_inventario){ return new EstadoInventarioResource($estado_inventario); }
    public function update(UpdateEstadoInventarioRequest $r, EstadoInventario $estado_inventario){ return new EstadoInventarioResource($this->service->update($estado_inventario,$r->validated())); }
    public function toggleVisible(int $id){ return new EstadoInventarioResource($this->service->toggleVisible($id)); }
    public function options(Request $r){ $solo = filter_var($r->query('solo_visibles','true'), FILTER_VALIDATE_BOOLEAN); return response()->json($this->service->options($solo)); }
}
