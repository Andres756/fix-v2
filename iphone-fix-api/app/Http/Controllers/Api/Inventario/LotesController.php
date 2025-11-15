<?php
// app/Http/Controllers/Api/Inventario/LotesController.php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\Lote;
use Illuminate\Http\Request;

class LotesController extends Controller
{
    /**
     * Listar todos los lotes
     */
    public function index(Request $request)
    {
        $perPage = min($request->integer('per_page', 15), 1000);
        
        $query = Lote::with('proveedor:id,nombre');
        
        if ($request->filled('proveedor_id')) {
            $query->where('proveedor_id', $request->proveedor_id);
        }
        
        $query->orderBy('created_at', 'desc');
        
        return response()->json($query->paginate($perPage));
    }
    
    /**
     * Opciones para select (id, nombre)
     */
    public function options()
    {
        $lotes = Lote::orderBy('created_at', 'desc')
            ->limit(100)
            ->get()
            ->map(function($lote) {
                return [
                    'id' => $lote->id,
                    'nombre' => $lote->numero_lote ?? "Lote #{$lote->id}"
                ];
            });
        
        return response()->json($lotes);
    }
    
    /**
     * Mostrar un lote específico
     */
    public function show($id)
    {
        $lote = Lote::with('proveedor')->findOrFail($id);
        return response()->json(['data' => $lote]);
    }
}