<?php
// app/Http/Controllers/Api/Inventario/LotesController.php
namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\Lote;
use App\Http\Resources\Inventario\LoteResource;
use App\Http\Requests\Inventario\Lotes\StoreLoteRequest;
use App\Http\Requests\Inventario\Lotes\UpdateLoteRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LotesController extends Controller
{
    public function index(Request $r){
        $per = min($r->integer('per_page',15),100);
        $q = Lote::with('proveedor');
        if ($s = $r->query('q')) $q->where('codigo_lote','like',"%$s%");
        return LoteResource::collection($q->paginate($per));
    }
    public function store(StoreLoteRequest $r){
        $x = Lote::create($r->validated());
        return (new LoteResource($x))->response()->setStatusCode(Response::HTTP_CREATED);
    }
    public function show(Lote $lote){ return new LoteResource($lote->load('proveedor')); }
    public function update(UpdateLoteRequest $r, Lote $lote){
        $lote->update($r->validated());
        return new LoteResource($lote->load('proveedor'));
    }
    public function destroy(Lote $lote){
        $lote->delete();
        return response()->noContent();
    }
}
