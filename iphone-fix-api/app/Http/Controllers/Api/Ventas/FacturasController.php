<?php
// app/Http/Controllers/Api/Ventas/FacturasController.php
namespace App\Http\Controllers\Api\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Ventas\Factura;
use App\Http\Resources\Ventas\FacturaResource;
use App\Http\Requests\Ventas\Facturas\StoreFacturaRequest;
use App\Http\Requests\Ventas\Facturas\UpdateFacturaRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FacturasController extends Controller
{
    public function index(Request $r){
        $per = min($r->integer('per_page',15),100);
        $q = Factura::with(['detalles','logs']);
        if ($cli = $r->query('cliente_id')) $q->where('cliente_id',$cli);
        if ($estado = $r->query('estado_id')) $q->where('estado_id',$estado);
        return FacturaResource::collection($q->paginate($per));
    }
    public function store(StoreFacturaRequest $r){
        $x = Factura::create($r->validated());
        return (new FacturaResource($x->load(['detalles','logs'])))->response()->setStatusCode(Response::HTTP_CREATED);
    }
    public function show(Factura $factura){ return new FacturaResource($factura->load(['detalles','logs'])); }
    public function update(UpdateFacturaRequest $r, Factura $factura){
        $factura->update($r->validated());
        return new FacturaResource($factura->load(['detalles','logs']));
    }
    public function destroy(Factura $factura){
        $factura->delete();
        return response()->noContent();
    }
}
