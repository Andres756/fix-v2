<?php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\EntradaProducto;
use App\Models\Inventario\EntradaProductoItem;
use App\Models\Inventario\Inventario;
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
            'lote_id' => 'nullable|exists:lotes,id',  // ✅ Lote opcional
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
            foreach ($request->items as $item) {
                $inventario = Inventario::find($item['inventario_id']);

                if (!$inventario) {
                    DB::rollBack();
                    return response()->json([
                        'message' => "El inventario con ID {$item['inventario_id']} no existe.",
                    ], 404);
                }

                // ✅ Regla 1: si el tipo de inventario es individual → solo una unidad
                if ($inventario->tipo_inventario_id == 1 && $item['cantidad'] > 1) {
                    DB::rollBack();
                    return response()->json([
                        'message' => "El producto '{$inventario->nombre}' es un equipo individual. Solo se permite ingresar 1 unidad por entrada.",
                    ], 422);
                }

                // ✅ Regla 2: si el inventario es individual (tipo 1) o tiene estado_inventario_id = 1 → no puede tener más de una entrada
                $entradaExistente = DB::table('entradas_producto_items')
                    ->join('entradas_producto', 'entradas_producto.id', '=', 'entradas_producto_items.entrada_id')
                    ->where('entradas_producto_items.inventario_id', $inventario->id)
                    ->exists();

                if ($entradaExistente && ($inventario->tipo_inventario_id == 1 || $inventario->estado_inventario_id == 1)) {
                    DB::rollBack();
                    return response()->json([
                        'message' => "El producto '{$inventario->nombre}' ya tiene una entrada registrada. No se permite duplicar entradas para inventarios individuales.",
                    ], 409);
                }
            }

            // ✅ Crear la entrada principal
            $entrada = EntradaProducto::create([
                'proveedor_id' => $request->proveedor_id,
                'lote_id' => $request->lote_id,
                'motivo_ingreso_id' => $request->motivo_ingreso_id,
                'fecha_entrada' => $request->fecha_entrada,
                'observaciones' => $request->observaciones,
            ]);

            // ✅ Crear los ítems (el trigger actualiza stock y registra movimiento)
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

            // ✅ IMPORTANTE: Revertir el stock antes de eliminar
            // Si tienes un trigger AFTER DELETE que lo hace automáticamente, 
            // puedes omitir este foreach. Si no, descomentar:
            
            /*
            foreach ($entrada->items as $item) {
                $inventario = Inventario::find($item->inventario_id);
                if ($inventario) {
                    $inventario->decrement('stock', $item->cantidad);
                }
            }
            */

            // Eliminar la entrada (esto también eliminará los items por CASCADE)
            $entrada->delete();

            DB::commit();

            return response()->json([
                'message' => 'Entrada eliminada exitosamente'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Entrada no encontrada'
            ], 404);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al eliminar la entrada',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}