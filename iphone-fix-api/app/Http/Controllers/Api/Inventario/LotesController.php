<?php
// app/Http/Controllers/Api/Inventario/LotesController.php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\Lote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LotesController extends Controller
{
    /**
     * Listar todos los lotes
     */
    public function index(Request $request)
    {
        $query = Lote::with('proveedor');

        if ($request->has('proveedor_id')) {
            $query->where('proveedor_id', $request->proveedor_id);
        }

        $perPage = $request->get('per_page', 15);
        $lotes = $query->orderBy('fecha_ingreso', 'desc')->paginate($perPage);

        return response()->json($lotes);
    }

    /**
     * Crear un nuevo lote
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numero_lote' => 'required|string|max:255|unique:lotes,numero_lote',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'costo_flete' => 'nullable|numeric|min:0',
            'fecha_ingreso' => 'required|date',
            'notas' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $lote = Lote::create($request->all());
        $lote->load('proveedor');

        return response()->json([
            'message' => 'Lote creado exitosamente',
            'data' => $lote
        ], 201);
    }

    /**
     * Mostrar un lote específico
     */
    public function show($id)
    {
        $lote = Lote::with(['proveedor', 'entradas.items.inventario'])->findOrFail($id);

        return response()->json(['data' => $lote]);
    }

    /**
     * Actualizar un lote
     */
    public function update(Request $request, $id)
    {
        $lote = Lote::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'numero_lote' => 'sometimes|string|max:255|unique:lotes,numero_lote,' . $id,
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'costo_flete' => 'nullable|numeric|min:0',
            'fecha_ingreso' => 'sometimes|date',
            'notas' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $lote->update($request->all());
        $lote->load('proveedor');

        return response()->json([
            'message' => 'Lote actualizado exitosamente',
            'data' => $lote
        ]);
    }

    /**
     * Eliminar un lote
     */
    public function destroy($id)
    {
        $lote = Lote::findOrFail($id);

        // Verificar si tiene entradas asociadas
        if ($lote->entradas()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar el lote porque tiene entradas asociadas'
            ], 400);
        }

        $lote->delete();

        return response()->json([
            'message' => 'Lote eliminado exitosamente'
        ]);
    }
}