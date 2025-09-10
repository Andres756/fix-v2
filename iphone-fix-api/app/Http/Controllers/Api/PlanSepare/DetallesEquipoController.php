<?php
// app/Http/Controllers/Api/Inventario/DetallesEquipoController.php
namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\DetalleEquipo;
use App\Http\Resources\Inventario\DetalleEquipoResource;
use App\Http\Requests\Inventario\Detalles\StoreDetalleEquipoRequest;
use App\Http\Requests\Inventario\Detalles\UpdateDetalleEquipoRequest;
use Symfony\Component\HttpFoundation\Response;

class DetallesEquipoController extends Controller
{
    public function store(StoreDetalleEquipoRequest $r){
        $x = DetalleEquipo::create($r->validated());
        return (new DetalleEquipoResource($x))->response()->setStatusCode(Response::HTTP_CREATED);
    }
    public function show(DetalleEquipo $detalles_equipo){ return new DetalleEquipoResource($detalles_equipo); }
    public function update(UpdateDetalleEquipoRequest $r, DetalleEquipo $detalles_equipo){
        $detalles_equipo->update($r->validated());
        return new DetalleEquipoResource($detalles_equipo);
    }
    public function destroy(DetalleEquipo $detalles_equipo){
        $detalles_equipo->delete();
        return response()->noContent();
    }
}
