<?php
// app/Http/Controllers/Api/Ventas/DetallesFacturaController.php
namespace App\Http\Controllers\Api\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Ventas\DetalleFactura;
use App\Http\Resources\Ventas\DetalleFacturaResource;
use App\Http\Requests\Ventas\Facturas\StoreDetalleFacturaRequest;
use App\Http\Requests\Ventas\Facturas\UpdateDetalleFacturaRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetallesFacturaController extends Controller
{
    public function index(Request $r){
        $per = min($r->integer('per_page',15),100);
        $q = DetalleFactura::query();
        if ($factura = $r->query('factura_id')) $q->where('factura_id',$factura);
        return DetalleFacturaResource::collection($q->paginate($per));
    }
    public function store(StoreDetalleFacturaRequest $r){
        $x = DetalleFactura::create($r->validated());
        return (new DetalleFacturaResource($x))->response()->setStatusCode(Response::HTTP_CREATED);
    }
    public function show(DetalleFactura $detalles_factura){
        return new DetalleFacturaResource($detalles_factura);
    }
    public function update(UpdateDetalleFacturaRequest $r, DetalleFactura $detalles_factura){
        $detalles_factura->update($r->validated());
        return new DetalleFacturaResource($detalles_factura);
    }
    public function destroy(DetalleFactura $detalles_factura){
        $detalles_factura->delete();
        return response()->noContent();
    }
}
