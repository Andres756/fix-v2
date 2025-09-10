<?php
// app/Http/Controllers/Api/Inventario/ProveedoresController.php
namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\Proveedor;
use App\Http\Resources\Inventario\ProveedorResource;
use App\Http\Requests\Inventario\Proveedores\StoreProveedorRequest;
use App\Http\Requests\Inventario\Proveedores\UpdateProveedorRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProveedoresController extends Controller
{
    public function index(Request $r){
        $per = min($r->integer('per_page',15),100);
        $q = Proveedor::query();
        if ($s = $r->query('q')) $q->where('nombre','like',"%$s%");
        return ProveedorResource::collection($q->paginate($per));
    }
    public function store(StoreProveedorRequest $r){
        $x = Proveedor::create($r->validated());
        return (new ProveedorResource($x))->response()->setStatusCode(Response::HTTP_CREATED);
    }
    public function show(Proveedor $proveedor){ return new ProveedorResource($proveedor); }
    public function update(UpdateProveedorRequest $r, Proveedor $proveedor){
        $proveedor->update($r->validated());
        return new ProveedorResource($proveedor);
    }
    public function destroy(Proveedor $proveedor){
        $proveedor->delete();
        return response()->noContent();
    }
}
