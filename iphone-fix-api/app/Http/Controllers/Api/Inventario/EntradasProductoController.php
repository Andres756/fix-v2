<?php
// app/Http/Controllers/Api/Inventario/EntradasProductoController.php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\EntradaProducto;
use App\Models\Inventario\EntradaProductoItem;
use App\Models\Inventario\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class EntradasProductoController extends Controller
{
    /**
     * Listar entradas con filtros
     */
    public function index(Request $request)
    {
        $query = EntradaProducto::with([
            'proveedor:id,nombre,nit',
            'cliente:id,nombre,documento',
            'lote:id,numero_lote',
            'motivoIngreso:id,nombre',
            'estadoEntrada:id,nombre,codigo,color',
            'usuario:id,name',
            'items.inventario:id,nombre,codigo'
        ]);

        // Filtros
        if ($request->has('inventario_id')) {
            $query->whereHas('items', function ($q) use ($request) {
                $q->where('inventario_id', $request->inventario_id);
            });
        }

        if ($request->has('tipo_entrada')) {
            $query->where('tipo_entrada', $request->tipo_entrada);
        }

        if ($request->has('proveedor_id')) {
            $query->where('tipo_entrada', 'proveedor')
                  ->where('proveedor_id', $request->proveedor_id);
        }

        if ($request->has('cliente_id')) {
            $query->where('tipo_entrada', 'cliente')
                  ->where('cliente_id', $request->cliente_id);
        }

        if ($request->has('lote_id')) {
            $query->where('lote_id', $request->lote_id);
        }

        if ($request->has('estado_entrada_id')) {
            $query->where('estado_entrada_id', $request->estado_entrada_id);
        }

        if ($request->has('fecha_desde')) {
            $query->whereDate('fecha_entrada', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta')) {
            $query->whereDate('fecha_entrada', '<=', $request->fecha_hasta);
        }

        $perPage = $request->get('per_page', 15);
        $entradas = $query->orderBy('fecha_entrada', 'desc')
                          ->orderBy('id', 'desc')
                          ->paginate($perPage);

        return response()->json($entradas);
    }

    /**
     * Crear nueva entrada
     */
    public function store(Request $request)
    {
        // Validación principal
        $validator = Validator::make($request->all(), [
            'tipo_entrada' => 'required|in:proveedor,cliente',
            'proveedor_id' => 'required_if:tipo_entrada,proveedor|nullable|exists:proveedores,id',
            'cliente_id' => 'required_if:tipo_entrada,cliente|nullable|exists:clientes,id',
            'lote_id' => 'nullable|exists:lotes,id',
            'motivo_ingreso_id' => 'required|integer|exists:motivos_movimientos,id',
            'estado_entrada_id' => 'nullable|exists:estados_entrada,id',
            'fecha_entrada' => 'required|date',
            'observaciones' => 'nullable|string|max:1000',
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

        // Validar que solo uno de proveedor_id o cliente_id esté presente
        if ($request->tipo_entrada === 'proveedor' && !$request->proveedor_id) {
            return response()->json([
                'message' => 'Debe seleccionar un proveedor para entradas de tipo proveedor.',
            ], 422);
        }

        if ($request->tipo_entrada === 'cliente' && !$request->cliente_id) {
            return response()->json([
                'message' => 'Debe seleccionar un cliente para entradas de tipo cliente.',
            ], 422);
        }

        // Validar que el motivo sea de tipo 'entrada'
        $motivo = DB::table('motivos_movimientos')
            ->where('id', $request->motivo_ingreso_id)
            ->where('tipo', 'entrada')
            ->first();

        if (!$motivo) {
            return response()->json([
                'message' => 'El motivo seleccionado no corresponde a una entrada de inventario.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Validaciones especiales para equipos individuales
            foreach ($request->items as $item) {
                $inventario = Inventario::find($item['inventario_id']);

                if (!$inventario) {
                    DB::rollBack();
                    return response()->json([
                        'message' => "El inventario con ID {$item['inventario_id']} no existe.",
                    ], 404);
                }

                // Regla 1: equipos individuales solo 1 unidad
                if ($inventario->tipo_inventario_id == 1 && $item['cantidad'] > 1) {
                    DB::rollBack();
                    return response()->json([
                        'message' => "El producto '{$inventario->nombre}' es un equipo individual. Solo se permite ingresar 1 unidad por entrada.",
                    ], 422);
                }

                // Regla 2: no permitir duplicar ingreso de equipos únicos
                if ($inventario->tipo_inventario_id == 1) {
                    $entradaExistente = DB::table('entradas_producto_items')
                        ->where('inventario_id', $inventario->id)
                        ->exists();

                    if ($entradaExistente || $inventario->stock > 0) {
                        DB::rollBack();
                        return response()->json([
                            'message' => "El producto '{$inventario->nombre}' ya tiene una entrada o stock disponible. No se permite duplicar ingresos para equipos individuales.",
                        ], 409);
                    }
                }
            }

            // Crear la entrada principal
            $entradaData = [
                'tipo_entrada' => $request->tipo_entrada,
                'lote_id' => $request->lote_id,
                'motivo_ingreso_id' => $request->motivo_ingreso_id,
                'estado_entrada_id' => $request->estado_entrada_id ?? 1,
                'fecha_entrada' => $request->fecha_entrada,
                'observaciones' => $request->observaciones,
                'usuario_id' => Auth::id(),
            ];

            // Asignar proveedor_id o cliente_id según tipo
            if ($request->tipo_entrada === 'proveedor') {
                $entradaData['proveedor_id'] = $request->proveedor_id;
                $entradaData['cliente_id'] = null;
            } else {
                $entradaData['cliente_id'] = $request->cliente_id;
                $entradaData['proveedor_id'] = null;
            }

            $entrada = EntradaProducto::create($entradaData);

            // Crear los ítems asociados
            foreach ($request->items as $item) {
                EntradaProductoItem::create([
                    'entrada_id' => $entrada->id,
                    'inventario_id' => $item['inventario_id'],
                    'cantidad' => $item['cantidad'],
                    'costo_unitario' => $item['costo_unitario'],
                ]);
            }

            DB::commit();

            // Cargar relaciones actualizadas
            $entrada->load([
                'proveedor:id,nombre,nit',
                'cliente:id,nombre,documento',
                'lote:id,numero_lote',
                'motivoIngreso:id,nombre',
                'estadoEntrada:id,nombre,codigo,color',
                'usuario:id,name',
                'items.inventario:id,nombre,codigo,stock,costo'
            ]);

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

    /**
     * Obtener una entrada específica
     */
    public function show($id)
    {
        $entrada = EntradaProducto::with([
            'proveedor:id,nombre,nit,telefono,correo',
            'cliente:id,nombre,documento,telefono,correo',
            'lote:id,numero_lote',
            'motivoIngreso:id,nombre',
            'estadoEntrada:id,nombre,codigo,color',
            'usuario:id,name,email',
            'items.inventario:id,nombre,codigo,stock,costo'
        ])->findOrFail($id);

        return response()->json(['data' => $entrada]);
    }

    /**
     * Actualizar estado de entrada
     */
    public function updateEstado(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'estado_entrada_id' => 'required|exists:estados_entrada,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $entrada = EntradaProducto::findOrFail($id);
        $entrada->update([
            'estado_entrada_id' => $request->estado_entrada_id
        ]);

        $entrada->load('estadoEntrada:id,nombre,codigo,color');

        return response()->json([
            'message' => 'Estado actualizado exitosamente',
            'data' => $entrada
        ]);
    }

    /**
     * Eliminar entrada (reversa stock automáticamente por trigger)
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $entrada = EntradaProducto::with('items')->findOrFail($id);
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

    /**
     * Buscar proveedores por NIT, nombre o contacto
     */
    public function buscarProveedores(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['data' => []]);
        }

        $proveedores = DB::table('proveedores')
            ->select('id', 'nombre', 'nit', 'tipo_documento', 'telefono', 'correo')
            ->where(function($q) use ($query) {
                $q->where('nombre', 'like', "%{$query}%")
                  ->orWhere('nit', 'like', "%{$query}%")
                  ->orWhere('contacto_nombre', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();

        return response()->json(['data' => $proveedores]);
    }

    /**
     * Buscar clientes por documento o nombre
     */
    public function buscarClientes(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['data' => []]);
        }

        $clientes = DB::table('clientes')
            ->select('id', 'nombre', 'documento', 'telefono', 'correo')
            ->where(function($q) use ($query) {
                $q->where('nombre', 'like', "%{$query}%")
                  ->orWhere('documento', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();

        return response()->json(['data' => $clientes]);
    }

    /**
     * Asignar o actualizar lote con distribución de flete
     */
    public function asignarLote(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'lote_id' => 'required|exists:lotes,id',
            'distribucion_flete' => 'nullable|array',
            'distribucion_flete.*.item_id' => 'required|exists:entradas_producto_items,id',
            'distribucion_flete.*.costo_flete_asignado' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $entrada = EntradaProducto::with('items')->findOrFail($id);
            
            // Obtener el lote
            $lote = DB::table('lotes')->where('id', $request->lote_id)->first();
            
            if (!$lote) {
                return response()->json([
                    'message' => 'Lote no encontrado'
                ], 404);
            }

            // Validar que la suma de flete distribuido no exceda el flete del lote
            if ($request->has('distribucion_flete')) {
                $fleteTotal = $lote->costo_flete ?? 0;
                $fleteDistribuido = collect($request->distribucion_flete)
                    ->sum('costo_flete_asignado');

                if ($fleteDistribuido > $fleteTotal) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'El flete distribuido excede el flete total del lote',
                        'flete_lote' => $fleteTotal,
                        'flete_distribuido' => $fleteDistribuido,
                    ], 422);
                }

                // Validar que todos los items pertenezcan a esta entrada
                $itemIds = $entrada->items->pluck('id')->toArray();
                $itemIdsDistribucion = collect($request->distribucion_flete)
                    ->pluck('item_id')
                    ->toArray();

                $invalidItems = array_diff($itemIdsDistribucion, $itemIds);
                if (!empty($invalidItems)) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Algunos items no pertenecen a esta entrada',
                        'invalid_items' => $invalidItems,
                    ], 422);
                }
            }

            // Actualizar el lote en la entrada
            $entrada->update([
                'lote_id' => $request->lote_id
            ]);

            // Actualizar la distribución de flete en los items
            if ($request->has('distribucion_flete')) {
                foreach ($request->distribucion_flete as $distribucion) {
                    DB::table('entradas_producto_items')
                        ->where('id', $distribucion['item_id'])
                        ->where('entrada_id', $entrada->id)
                        ->update([
                            'costo_flete_asignado' => $distribucion['costo_flete_asignado'],
                            'updated_at' => now(),
                        ]);
                }
            }

            // Recalcular el costo promedio ponderado para cada producto afectado
            // usando el nuevo costo_total_item que incluye el flete
            foreach ($entrada->items as $item) {
                $this->recalcularCostoPromedio($item->inventario_id);
            }

            DB::commit();

            // Recargar la entrada con todas las relaciones
            $entrada->load([
                'proveedor:id,nombre,nit',
                'cliente:id,nombre,documento',
                'lote:id,numero_lote,costo_flete',
                'motivoIngreso:id,nombre',
                'estadoEntrada:id,nombre,codigo,color',
                'usuario:id,name',
                'items.inventario:id,nombre,codigo,stock,costo'
            ]);

            return response()->json([
                'message' => 'Lote asignado y flete distribuido correctamente',
                'data' => $entrada
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al asignar el lote',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recalcular el costo promedio ponderado de un inventario
     * considerando el costo_total_item (que incluye flete)
     */
    private function recalcularCostoPromedio($inventarioId)
    {
        // Obtener todas las entradas de este inventario
        $items = DB::table('entradas_producto_items')
            ->where('inventario_id', $inventarioId)
            ->get();

        if ($items->isEmpty()) {
            return;
        }

        $stockTotal = 0;
        $costoTotalAcumulado = 0;

        foreach ($items as $item) {
            $stockTotal += $item->cantidad;
            
            // Usar costo_total_item si está disponible, sino calcular manualmente
            $costoItem = $item->costo_total_item ?? 
                (($item->costo_unitario * $item->cantidad) + ($item->costo_flete_asignado ?? 0));
            
            $costoTotalAcumulado += $costoItem;
        }

        // Calcular costo promedio ponderado
        $costoPromedio = $stockTotal > 0 ? ($costoTotalAcumulado / $stockTotal) : 0;

        // Actualizar el inventario
        DB::table('inventarios')
            ->where('id', $inventarioId)
            ->update([
                'costo' => $costoPromedio,
                'updated_at' => now(),
            ]);
    }
}