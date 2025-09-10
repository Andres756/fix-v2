<?php

namespace App\Http\Controllers\Api\Parametros\Facturacion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parametros\ParametrosFacturacion\StoreParametroFacturacionRequest;
use App\Http\Requests\Parametros\ParametrosFacturacion\UpdateParametroFacturacionRequest;
use App\Http\Resources\Parametros\ParametroFacturacionResource;
use App\Models\Parametros\ParametroFacturacion;
use App\Services\Parametros\ParametrosFacturacionService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ParametrosFacturacionController extends Controller
{
    public function __construct(private ParametrosFacturacionService $service) {}

    public function index(Request $r){ return ParametroFacturacionResource::collection($this->service->list($r->all())); }
    public function store(StoreParametroFacturacionRequest $r){ $x=$this->service->create($r->validated()); return (new ParametroFacturacionResource($x))->response()->setStatusCode(Response::HTTP_CREATED); }
    public function show(ParametroFacturacion $parametro_facturacion){ return new ParametroFacturacionResource($parametro_facturacion); }
    public function update(UpdateParametroFacturacionRequest $r, ParametroFacturacion $parametro_facturacion){ return new ParametroFacturacionResource($this->service->update($parametro_facturacion,$r->validated())); }

    // helper para consumir por clave
    public function byClave(string $clave)
    {
        $item = $this->service->getByClave($clave);
        abort_if(!$item, 404, 'No existe la clave');
        return new ParametroFacturacionResource($item);
    }
}
