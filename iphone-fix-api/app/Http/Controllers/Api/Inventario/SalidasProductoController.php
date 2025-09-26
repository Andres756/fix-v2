<?php
// app/Http/Controllers/Api/Inventario/SalidasProductoController.php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\SalidaProducto;
use App\Http\Resources\Inventario\SalidaProductoResource;
use App\Http\Requests\Inventario\Salidas\StoreSalidaProductoRequest;
use App\Http\Requests\Inventario\Salidas\UpdateSalidaProductoRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SalidasProductoController extends Controller
{
    public function index(Request $r)
    {
        $perPage = min($r->integer('per_page', 15), 100);
        
        $q = SalidaProducto::with([
            'inventario:id,nombre,codigo,stock'
        ]);

        // Filtros opcionales
        if ($r->filled('inventario_id')) {
            $q->where('inventario_id', $r->input('inventario_id'));
        }

        if ($r->filled('tipo_salida')) {
            $q->where('tipo_salida', $r->input('tipo_salida'));
        }

        if ($r->filled('fecha_desde')) {
            $q->whereDate('fecha_salida', '>=', $r->input('fecha_desde'));
        }

        if ($r->filled('fecha_hasta')) {
            $q->whereDate('fecha_salida', '<=', $r->input('fecha_hasta'));
        }

        $q->orderBy('fecha_salida', 'desc');

        return SalidaProductoResource::collection($q->paginate($perPage));
    }

    public function store(StoreSalidaProductoRequest $r)
    {
        // El trigger AFTER INSERT se encarga de actualizar stock y estado automáticamente
        
        $salida = SalidaProducto::create($r->validated());

        return (new SalidaProductoResource(
            $salida->load('inventario:id,nombre,codigo,stock')
        ))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(SalidaProducto $salidas_producto)
    {
        return new SalidaProductoResource(
            $salidas_producto->load('inventario:id,nombre,codigo,stock')
        );
    }

    public function update(UpdateSalidaProductoRequest $r, SalidaProducto $salidas_producto)
    {
        $salidas_producto->update($r->validated());
        
        return new SalidaProductoResource(
            $salidas_producto->load('inventario:id,nombre,codigo,stock')
        );
    }

    public function destroy(SalidaProducto $salidas_producto)
    {
        // El trigger AFTER DELETE devolverá el stock automáticamente
        $salidas_producto->delete();
        
        return response()->noContent();
    }
}