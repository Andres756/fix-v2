<?php
// app/Http/Controllers/Api/Ventas/DescuentosController.php
namespace App\Http\Controllers\Api\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Ventas\Descuento;
use App\Http\Resources\Ventas\DescuentoResource;
use App\Http\Requests\Ventas\Descuentos\StoreDescuentoRequest;
use App\Http\Requests\Ventas\Descuentos\UpdateDescuentoRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DescuentosController extends Controller
{
    public function index(Request $r){
        $per = min($r->integer('per_page',15),100);
        $q = Descuento::query();
        if ($s = $r->query('q')) $q->where('nombre','like',"%$s%");
        return DescuentoResource::collection($q->paginate($per));
    }
    public function store(StoreDescuentoRequest $r){
        $x = Descuento::create($r->validated());
        return (new DescuentoResource($x))->response()->setStatusCode(Response::HTTP_CREATED);
    }
    public function show(Descuento $descuento){ return new DescuentoResource($descuento); }
    public function update(UpdateDescuentoRequest $r, Descuento $descuento){
        $descuento->update($r->validated());
        return new DescuentoResource($descuento);
    }
    public function destroy(Descuento $descuento){
        $descuento->delete();
        return response()->noContent();
    }
}
