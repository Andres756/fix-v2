<?php
// app/Http/Controllers/Api/Inventario/EntradasProductoController.php
namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\EntradaProducto;
use App\Http\Resources\Inventario\EntradaProductoResource;
use App\Http\Requests\Inventario\Entradas\StoreEntradaProductoRequest;
use App\Http\Requests\Inventario\Entradas\UpdateEntradaProductoRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EntradasProductoController extends Controller
{
    public function index(Request $r){
        $per = min($r->integer('per_page',15),100);
        $q = EntradaProducto::with(['inventario','lote']);
        return EntradaProductoResource::collection($q->paginate($per));
    }
    public function store(StoreEntradaProductoRequest $r){
        $x = EntradaProducto::create($r->validated());
        return (new EntradaProductoResource($x->load(['inventario','lote'])))->response()->setStatusCode(Response::HTTP_CREATED);
    }
    public function show(EntradaProducto $entradas_producto){
        return new EntradaProductoResource($entradas_producto->load(['inventario','lote']));
    }
    public function update(UpdateEntradaProductoRequest $r, EntradaProducto $entradas_producto){
        $entradas_producto->update($r->validated());
        return new EntradaProductoResource($entradas_producto->load(['inventario','lote']));
    }
    public function destroy(EntradaProducto $entradas_producto){
        $entradas_producto->delete();
        return response()->noContent();
    }
}
