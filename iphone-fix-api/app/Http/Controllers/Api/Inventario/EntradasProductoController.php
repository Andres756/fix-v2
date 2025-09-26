<?php
// app/Http/Controllers/Api/Inventario/EntradasProductoController.php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\EntradaProducto;
use App\Models\Inventario\Inventario;
use App\Http\Resources\Inventario\EntradaProductoResource;
use App\Http\Requests\Inventario\Entradas\StoreEntradaProductoRequest;
use App\Http\Requests\Inventario\Entradas\UpdateEntradaProductoRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class EntradasProductoController extends Controller
{
    public function index(Request $r)
    {
        $perPage = min($r->integer('per_page', 15), 100);
        
        $q = EntradaProducto::with([
            'inventario:id,nombre,codigo,stock,costo',
            'lote:id,numero_lote',
            'lote.proveedor:id,nombre',
            'motivo:id,nombre'
        ]);

        // Filtros opcionales
        if ($r->filled('inventario_id')) {
            $q->where('inventario_id', $r->input('inventario_id'));
        }

        if ($r->filled('lote_id')) {
            $q->where('lote_id', $r->input('lote_id'));
        }

        $q->orderBy('fecha_entrada', 'desc');

        return EntradaProductoResource::collection($q->paginate($perPage));
    }

    public function store(StoreEntradaProductoRequest $r)
    {
        // LOS TRIGGERS SE ENCARGAN DE TODO AUTOMÁTICAMENTE
        // Solo insertamos la entrada y los triggers actualizan stock, costo y estado
        
        $entrada = EntradaProducto::create($r->validated());

        return (new EntradaProductoResource(
            $entrada->load([
                'inventario:id,nombre,codigo,stock,costo',
                'lote:id,numero_lote',
                'lote.proveedor:id,nombre',
                'motivo:id,nombre'
            ])
        ))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(EntradaProducto $entradas_producto)
    {
        return new EntradaProductoResource(
            $entradas_producto->load([
                'inventario:id,nombre,codigo,stock,costo',
                'lote:id,numero_lote',
                'motivo:id,nombre'
            ])
        );
    }

    public function update(UpdateEntradaProductoRequest $r, EntradaProducto $entradas_producto)
    {
        // ADVERTENCIA: Actualizar entradas puede descuadrar el inventario
        // Los triggers recalcularán todo, pero es mejor evitar ediciones
        
        $entradas_producto->update($r->validated());
        
        return new EntradaProductoResource(
            $entradas_producto->load([
                'inventario:id,nombre,codigo,stock,costo',
                'lote:id,numero_lote',
                'motivo:id,nombre'
            ])
        );
    }

    public function destroy(EntradaProducto $entradas_producto)
    {
        // El trigger AFTER DELETE recalculará automáticamente stock y costo
        $entradas_producto->delete();
        
        return response()->noContent();
    }
}