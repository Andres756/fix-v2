<?php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\EntradaProducto;
use App\Models\Inventario\EntradaProductoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EntradasProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = EntradaProducto::with(['proveedor', 'lote', 'motivoIngreso', 'items.inventario']);

        if ($request->has('inventario_id')) {
            $query->whereHas('items', function ($q) use ($request) {
                $q->where('inventario_id', $request->inventario_id);
            });
        }

        if ($request->has('lote_id')) {
            $query->where('lote_id', $request->lote_id);
        }

        $perPage = $request->get('per_page', 15);
        $entradas = $query->orderBy('fecha_entrada', 'desc')->paginate($perPage);

        return response()->json($entradas);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'proveedor_id' => 'required|exists:proveedores,id',
            'lote_id' => 'required|exists:lotes,id',
            'motivo_ingreso_id' => 'required|exists:motivos_ingreso,id',
            'fecha_entrada' => 'required|date',
            'observaciones' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.inventario_id' => 'required|exists:inventarios,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.costo_unitario' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Crear la entrada principal
            $entrada = EntradaProducto::create([
                'proveedor_id' => $request->proveedor_id,
                'lote_id' => $request->lote_id,
                'motivo_ingreso_id' => $request->motivo_ingreso_id,
                'fecha_entrada' => $request->fecha_entrada,
                'observaciones' => $request->observaciones,
            ]);

            // Registrar cada item (el trigger se encarga del stock y movimientos)
            foreach ($request->items as $item) {
                EntradaProductoItem::create([
                    'entrada_id' => $entrada->id,
                    'inventario_id' => $item['inventario_id'],
                    'cantidad' => $item['cantidad'],
                    'costo_unitario' => $item['costo_unitario'],
                ]);
            }

            DB::commit();

            $entrada->load(['proveedor', 'lote', 'motivoIngreso', 'items.inventario']);

            return response()->json([
                'message' => 'Entrada de inventario registrada exitosamente',
                'data' => $entrada
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al registrar la entrada',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $entrada = EntradaProducto::with(['proveedor', 'lote', 'motivoIngreso', 'items.inventario'])
            ->findOrFail($id);

        return response()->json(['data' => $entrada]);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $entrada = EntradaProducto::with('items')->findOrFail($id);

            // Solo eliminamos la entrada; el trigger AFTER DELETE revertirá el stock
            $entrada->delete();

            DB::commit();

            return response()->json([
                'message' => 'Entrada eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al eliminar la entrada',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
