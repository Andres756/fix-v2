<?php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CostosAdicionalesController extends Controller
{
    /**
     * Obtener todos los costos adicionales de una entrada
     * GET /inventario/entradas/{entrada_id}/costos-adicionales
     */
    public function index($entradaId)
    {
        try {
            // Verificar que la entrada existe
            $entrada = DB::table('entradas_producto')->where('id', $entradaId)->first();
            
            if (!$entrada) {
                return response()->json([
                    'message' => 'Entrada no encontrada'
                ], 404);
            }

            // Obtener repuestos de inventario
            $repuestosInventario = DB::table('entradas_costos_repuestos_inventario as ecri')
                ->select([
                    'ecri.*',
                    'i.nombre as inventario_nombre',
                    'i.codigo as inventario_codigo',
                ])
                ->leftJoin('inventarios as i', 'ecri.inventario_id', '=', 'i.id')
                ->where('ecri.entrada_id', $entradaId)
                ->get();

            // Obtener repuestos externos
            $repuestosExternos = DB::table('entradas_costos_repuestos_externos as ecre')
                ->select([
                    'ecre.*',
                    'p.nombre as proveedor_nombre',
                ])
                ->leftJoin('proveedores as p', 'ecre.proveedor_id', '=', 'p.id')
                ->where('ecre.entrada_id', $entradaId)
                ->get();

            // Obtener pagos a técnicos
            $pagosTecnicos = DB::table('entradas_costos_tecnicos as ect')
                ->select([
                    'ect.*',
                    'u.name as tecnico_nombre',
                ])
                ->leftJoin('users as u', 'ect.tecnico_id', '=', 'u.id')
                ->where('ect.entrada_id', $entradaId)
                ->get();

            return response()->json([
                'data' => [
                    'repuestos_inventario' => $repuestosInventario,
                    'repuestos_externos' => $repuestosExternos,
                    'pagos_tecnicos' => $pagosTecnicos,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener costos adicionales',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener resumen de costos adicionales
     * GET /inventario/entradas/{entrada_id}/costos-adicionales/resumen
     */
    public function resumen($entradaId)
    {
        try {
            $entrada = DB::table('entradas_producto')->where('id', $entradaId)->first();
            
            if (!$entrada) {
                return response()->json([
                    'message' => 'Entrada no encontrada'
                ], 404);
            }

            // Calcular totales
            $totalRepuestosInventario = DB::table('entradas_costos_repuestos_inventario')
                ->where('entrada_id', $entradaId)
                ->sum('costo_total');

            $totalRepuestosExternos = DB::table('entradas_costos_repuestos_externos')
                ->where('entrada_id', $entradaId)
                ->sum('costo_total');

            $totalPagosTecnicos = DB::table('entradas_costos_tecnicos')
                ->where('entrada_id', $entradaId)
                ->sum('costo_calculado');

            // ✅ Calcular costo base desde los items de la entrada
            $costoBase = DB::table('entradas_producto_items')
                ->where('entrada_id', $entradaId)
                ->selectRaw('SUM(costo_unitario * cantidad + IFNULL(costo_flete_asignado, 0)) as total')
                ->value('total') ?? 0;

            $costoAdicionalRepuestos = $totalRepuestosInventario + $totalRepuestosExternos;
            $costoAdicionalTecnicos = $totalPagosTecnicos;
            $costoTotalFinal = $costoBase + $costoAdicionalRepuestos + $costoAdicionalTecnicos;

            return response()->json([
                'data' => [
                    'costo_base' => (float)$costoBase,
                    'repuestos_inventario' => (float)$totalRepuestosInventario,
                    'repuestos_externos' => (float)$totalRepuestosExternos,
                    'total_repuestos' => (float)$costoAdicionalRepuestos,
                    'total_tecnicos' => (float)$costoAdicionalTecnicos,
                    'costo_total_final' => (float)$costoTotalFinal,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al calcular resumen',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener historial de costos de un inventario
     * GET /inventario/items/{inventario_id}/historial-costos
     */
    public function historialCostos($inventarioId)
    {
        try {
            $historial = DB::table('inventarios_historial_costos')
                ->where('inventario_id', $inventarioId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($registro) {
                    // Decodificar JSON
                    $desglose = json_decode($registro->desglose_json, true);
                    
                    return [
                        'id' => $registro->id,
                        'inventario_id' => $registro->inventario_id,
                        'entrada_id' => $registro->entrada_id,
                        'costo_base' => (float)$registro->costo_base,
                        'costo_flete' => (float)$registro->costo_flete,
                        'costo_repuestos_inventario' => (float)$registro->costo_repuestos_inventario,
                        'costo_repuestos_externos' => (float)$registro->costo_repuestos_externos,
                        'costo_tecnicos' => (float)$registro->costo_tecnicos,
                        'costo_total' => (float)$registro->costo_total,
                        'desglose' => $desglose,
                        'motivo' => $registro->motivo,
                        'tipo_operacion' => $registro->tipo_operacion,
                        'usuario_id' => $registro->usuario_id,
                        'created_at' => $registro->created_at,
                    ];
                });

            return response()->json([
                'data' => $historial
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener historial de costos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Agregar repuesto de inventario
     * POST /inventario/entradas/{entrada_id}/costos-adicionales/repuestos-inventario
     */
    public function addRepuestoInventario(Request $request, $entradaId)
    {
        $validator = Validator::make($request->all(), [
            'inventario_id' => 'required|exists:inventarios,id',
            'cantidad' => 'required|integer|min:1',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Verificar que la entrada existe y es tipo cliente
            $entrada = DB::table('entradas_producto')->where('id', $entradaId)->first();
            
            if (!$entrada) {
                DB::rollBack();
                return response()->json(['message' => 'Entrada no encontrada'], 404);
            }

            if ($entrada->tipo_entrada !== 'cliente') {
                DB::rollBack();
                return response()->json([
                    'message' => 'Solo se pueden agregar costos adicionales a entradas tipo cliente'
                ], 422);
            }

            // Obtener información del inventario
            $inventario = DB::table('inventarios')->where('id', $request->inventario_id)->first();
            
            if (!$inventario) {
                DB::rollBack();
                return response()->json(['message' => 'Inventario no encontrado'], 404);
            }

            // Verificar stock disponible
            if ($inventario->stock < $request->cantidad) {
                DB::rollBack();
                return response()->json([
                    'message' => "Stock insuficiente. Disponible: {$inventario->stock}, Solicitado: {$request->cantidad}"
                ], 422);
            }

            $costoUnitario = $inventario->costo ?? 0;
            $costoTotal = $costoUnitario * $request->cantidad;

            // Crear registro de costo adicional
            $costoId = DB::table('entradas_costos_repuestos_inventario')->insertGetId([
                'entrada_id' => $entradaId,
                'inventario_id' => $request->inventario_id,
                'cantidad' => $request->cantidad,
                'costo_unitario' => $costoUnitario,
                'costo_total' => $costoTotal,
                'observaciones' => $request->observaciones,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // ✅ Crear salida de inventario automática
            $this->crearMovimientoInventario(
                $request->inventario_id,
                'salida',
                $request->cantidad,
                $costoUnitario,
                $costoId,
                "Repuesto usado en compra de equipo (Entrada #{$entradaId})"
            );
            
            // Recalcular totales de la entrada
            $this->recalcularTotales($entradaId);

            DB::commit();

            // Obtener el registro creado con relaciones
            $costo = DB::table('entradas_costos_repuestos_inventario as ecri')
                ->select([
                    'ecri.*',
                    'i.nombre as inventario_nombre',
                    'i.codigo as inventario_codigo',
                ])
                ->leftJoin('inventarios as i', 'ecri.inventario_id', '=', 'i.id')
                ->where('ecri.id', $costoId)
                ->first();

            return response()->json([
                'message' => 'Repuesto agregado correctamente',
                'data' => $costo
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al agregar repuesto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Agregar repuesto externo
     * POST /inventario/entradas/{entrada_id}/costos-adicionales/repuestos-externos
     */
    public function addRepuestoExterno(Request $request, $entradaId)
    {
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:255',
            'cantidad' => 'required|integer|min:1',
            'costo_unitario' => 'required|numeric|min:0',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'observaciones' => 'nullable|string|max:1000',
            'a_credito' => 'nullable|boolean',
            'metodo_pago_id' => 'nullable|exists:formas_pago,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Verificar entrada
            $entrada = DB::table('entradas_producto')->where('id', $entradaId)->first();
            
            if (!$entrada) {
                DB::rollBack();
                return response()->json(['message' => 'Entrada no encontrada'], 404);
            }

            if ($entrada->tipo_entrada !== 'cliente') {
                DB::rollBack();
                return response()->json([
                    'message' => 'Solo se pueden agregar costos adicionales a entradas tipo cliente'
                ], 422);
            }

            $costoTotal = $request->cantidad * $request->costo_unitario;
            $aCredito = $request->a_credito ?? false;

            // Crear registro de costo
            $costoId = DB::table('entradas_costos_repuestos_externos')->insertGetId([
                'entrada_id' => $entradaId,
                'proveedor_id' => $request->proveedor_id,
                'descripcion' => $request->descripcion,
                'cantidad' => $request->cantidad,
                'costo_unitario' => $request->costo_unitario,
                'costo_total' => $costoTotal,
                'observaciones' => $request->observaciones,
                'a_credito' => $aCredito,
                'metodo_pago_id' => $request->metodo_pago_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Recalcular totales
            $this->recalcularTotales($entradaId);

            DB::commit();

            // Obtener el registro creado
            $costo = DB::table('entradas_costos_repuestos_externos as ecre')
                ->select([
                    'ecre.*',
                    'p.nombre as proveedor_nombre',
                    'fp.nombre as metodo_pago_nombre',
                ])
                ->leftJoin('proveedores as p', 'ecre.proveedor_id', '=', 'p.id')
                ->leftJoin('formas_pago as fp', 'ecre.metodo_pago_id', '=', 'fp.id')
                ->where('ecre.id', $costoId)
                ->first();

            return response()->json([
                'message' => 'Repuesto externo agregado correctamente',
                'data' => $costo
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al agregar repuesto externo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Agregar pago a técnico
     * POST /inventario/entradas/{entrada_id}/costos-adicionales/pagos-tecnicos
     */
    public function addPagoTecnico(Request $request, $entradaId)
    {
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:255',
            'tipo_pago' => 'required|in:fijo,porcentaje',
            'valor' => 'required|numeric|min:0',
            'tecnico_id' => 'nullable|exists:users,id',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Verificar entrada
            $entrada = DB::table('entradas_producto')->where('id', $entradaId)->first();
            
            if (!$entrada) {
                DB::rollBack();
                return response()->json(['message' => 'Entrada no encontrada'], 404);
            }

            if ($entrada->tipo_entrada !== 'cliente') {
                DB::rollBack();
                return response()->json([
                    'message' => 'Solo se pueden agregar costos adicionales a entradas tipo cliente'
                ], 422);
            }

            // Calcular costo según tipo de pago
            $costoCalculado = 0;
            
            if ($request->tipo_pago === 'fijo') {
                $costoCalculado = $request->valor;
            } else { // porcentaje
                // ✅ Calcular costo base desde items
                $costoBase = DB::table('entradas_producto_items')
                    ->where('entrada_id', $entradaId)
                    ->selectRaw('SUM(costo_unitario * cantidad + IFNULL(costo_flete_asignado, 0)) as total')
                    ->value('total') ?? 0;
                $costoCalculado = ($costoBase * $request->valor) / 100;
            }

            // Crear registro
            $pagoId = DB::table('entradas_costos_tecnicos')->insertGetId([
                'entrada_id' => $entradaId,
                'tecnico_id' => $request->tecnico_id,
                'descripcion' => $request->descripcion,
                'tipo_pago' => $request->tipo_pago,
                'valor' => $request->valor,
                'costo_calculado' => $costoCalculado,
                'observaciones' => $request->observaciones,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Recalcular totales
            $this->recalcularTotales($entradaId);

            DB::commit();

            // Obtener el registro creado
            $pago = DB::table('entradas_costos_tecnicos as ect')
                ->select([
                    'ect.*',
                    'u.name as tecnico_nombre',
                ])
                ->leftJoin('users as u', 'ect.tecnico_id', '=', 'u.id')
                ->where('ect.id', $pagoId)
                ->first();

            return response()->json([
                'message' => 'Pago a técnico agregado correctamente',
                'data' => $pago
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al agregar pago a técnico',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un costo adicional
     * DELETE /inventario/entradas/{entrada_id}/costos-adicionales/{tipo}/{id}
     */
    public function destroy($entradaId, $tipo, $id)
    {
        $tablas = [
            'repuesto-inventario' => 'entradas_costos_repuestos_inventario',
            'repuesto-externo' => 'entradas_costos_repuestos_externos',
            'pago-tecnico' => 'entradas_costos_tecnicos',
        ];

        if (!isset($tablas[$tipo])) {
            return response()->json([
                'message' => 'Tipo de costo no válido'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $tabla = $tablas[$tipo];
            
            // Si es repuesto de inventario, revertir el movimiento
            if ($tipo === 'repuesto-inventario') {
                $costo = DB::table($tabla)
                    ->where('id', $id)
                    ->where('entrada_id', $entradaId)
                    ->first();
                
                if ($costo) {
                    // Revertir movimiento de inventario
                    $this->revertirMovimientoInventario($costo->id, $costo->inventario_id, $costo->cantidad);
                }
            }
            
            $deleted = DB::table($tabla)
                ->where('id', $id)
                ->where('entrada_id', $entradaId)
                ->delete();

            if (!$deleted) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Costo no encontrado'
                ], 404);
            }

            // Recalcular totales
            $this->recalcularTotales($entradaId);

            DB::commit();

            return response()->json([
                'message' => 'Costo eliminado correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al eliminar costo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recalcular totales de la entrada
     * Método privado usado internamente
     */
    private function recalcularTotales($entradaId)
    {
        // Calcular totales
        $totalRepuestosInventario = DB::table('entradas_costos_repuestos_inventario')
            ->where('entrada_id', $entradaId)
            ->sum('costo_total');

        $totalRepuestosExternos = DB::table('entradas_costos_repuestos_externos')
            ->where('entrada_id', $entradaId)
            ->sum('costo_total');

        $totalPagosTecnicos = DB::table('entradas_costos_tecnicos')
            ->where('entrada_id', $entradaId)
            ->sum('costo_calculado');

        $costoAdicionalRepuestos = $totalRepuestosInventario + $totalRepuestosExternos;
        $costoAdicionalTecnicos = $totalPagosTecnicos;

        // ✅ Calcular costo base desde items
        $costoBase = DB::table('entradas_producto_items')
            ->where('entrada_id', $entradaId)
            ->selectRaw('SUM(costo_unitario * cantidad + IFNULL(costo_flete_asignado, 0)) as total')
            ->value('total') ?? 0;
        $costoTotalFinal = $costoBase + $costoAdicionalRepuestos + $costoAdicionalTecnicos;

        // Actualizar entrada
        DB::table('entradas_producto')
            ->where('id', $entradaId)
            ->update([
                'costo_adicional_repuestos' => $costoAdicionalRepuestos,
                'costo_adicional_tecnicos' => $costoAdicionalTecnicos,
                'costo_total_final' => $costoTotalFinal,
                'updated_at' => now(),
            ]);

        // ✅ Actualizar costos en inventarios
        $this->actualizarCostosInventarios($entradaId);
        
        // ✅ Registrar en historial de costos
        $this->registrarHistorialCostos($entradaId);
    }

    /**
     * Actualizar costos de los items en la tabla inventarios
     * Distribuye los costos adicionales proporcionalmente
     */
    private function actualizarCostosInventarios($entradaId)
    {
        // Obtener entrada
        $entrada = DB::table('entradas_producto')->where('id', $entradaId)->first();
        
        // Solo aplicar para entradas tipo cliente
        if (!$entrada || $entrada->tipo_entrada !== 'cliente') {
            return;
        }

        // Obtener items de la entrada
        $items = DB::table('entradas_producto_items')
            ->where('entrada_id', $entradaId)
            ->get();

        if ($items->isEmpty()) {
            return;
        }

        // Obtener costos adicionales
        $costoAdicionalRepuestos = $entrada->costo_adicional_repuestos ?? 0;
        $costoAdicionalTecnicos = $entrada->costo_adicional_tecnicos ?? 0;
        $totalCostosAdicionales = $costoAdicionalRepuestos + $costoAdicionalTecnicos;

        // Si no hay costos adicionales, usar costo base
        if ($totalCostosAdicionales == 0) {
            foreach ($items as $item) {
                $costoFinal = $item->costo_unitario;
                
                DB::table('inventarios')
                    ->where('id', $item->inventario_id)
                    ->update([
                        'costo' => $costoFinal,
                        'updated_at' => now(),
                    ]);
            }
            return;
        }

        // Calcular costo base total de todos los items (sin flete)
        $costoBaseTotal = 0;
        foreach ($items as $item) {
            $costoBaseTotal += $item->costo_unitario * $item->cantidad;
        }

        // Si hay un solo item, asignarle todos los costos adicionales
        if ($items->count() === 1) {
            $item = $items->first();
            $costoFinal = $item->costo_unitario + $totalCostosAdicionales;
            
            DB::table('inventarios')
                ->where('id', $item->inventario_id)
                ->update([
                    'costo' => $costoFinal,
                    'updated_at' => now(),
                ]);
            return;
        }

        // Si hay múltiples items, distribuir proporcionalmente
        foreach ($items as $item) {
            $costoBaseItem = $item->costo_unitario * $item->cantidad;
            
            // Calcular proporción de este item
            $proporcion = $costoBaseTotal > 0 ? ($costoBaseItem / $costoBaseTotal) : 0;
            
            // Calcular costos adicionales asignados a este item
            $costosAdicionalesItem = $totalCostosAdicionales * $proporcion;
            
            // Costo final unitario = costo base + (costos adicionales / cantidad)
            $costoFinal = $item->costo_unitario + ($costosAdicionalesItem / $item->cantidad);
            
            DB::table('inventarios')
                ->where('id', $item->inventario_id)
                ->update([
                    'costo' => $costoFinal,
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Crear movimiento de inventario (salida)
     */
    private function crearMovimientoInventario(
        $inventarioId,
        $tipoMovimiento,
        $cantidad,
        $costoUnitario,
        $costoAdicionalId,
        $observaciones
    ) {
        // Obtener stock actual
        $inventario = DB::table('inventarios')->where('id', $inventarioId)->first();
        
        if (!$inventario) {
            throw new \Exception('Inventario no encontrado');
        }

        $stockAnterior = $inventario->stock;
        $stockNuevo = $tipoMovimiento === 'entrada' 
            ? $stockAnterior + $cantidad 
            : $stockAnterior - $cantidad;

        // Obtener ID del motivo
        $motivoCodigo = $tipoMovimiento === 'entrada' 
            ? 'entrada_devolucion_os' 
            : 'salida_costo_adicional';
        
        $motivo = DB::table('motivos_movimientos')
            ->where('codigo', $motivoCodigo)
            ->where('activo', 1)
            ->first();

        if (!$motivo) {
            throw new \Exception("Motivo '{$motivoCodigo}' no encontrado. Ejecuta el script SQL para agregarlo.");
        }

        // Crear movimiento
        DB::table('movimientos_inventario')->insert([
            'inventario_id' => $inventarioId,
            'tipo_movimiento' => $tipoMovimiento,
            'cantidad' => $cantidad,
            'stock_anterior' => $stockAnterior,
            'stock_nuevo' => $stockNuevo,
            'costo_unitario' => $costoUnitario,
            'entrada_id' => null,
            'venta_id' => null,
            'orden_servicio_id' => null,
            'traslado_id' => null,
            'ajuste_id' => null,
            'costo_adicional_id' => $costoAdicionalId,
            'motivo_id' => $motivo->id,
            'documento_referencia' => null,
            'observaciones' => $observaciones,
            'usuario_id' => auth()->id() ?? 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Actualizar stock en inventarios
        DB::table('inventarios')
            ->where('id', $inventarioId)
            ->update([
                'stock' => $stockNuevo,
                'updated_at' => now(),
            ]);
    }

    /**
     * Revertir movimiento de inventario al eliminar costo
     */
    private function revertirMovimientoInventario($costoAdicionalId, $inventarioId, $cantidad)
    {
        // Buscar el movimiento de salida original
        $movimiento = DB::table('movimientos_inventario')
            ->where('costo_adicional_id', $costoAdicionalId)
            ->where('tipo_movimiento', 'salida')
            ->first();

        if (!$movimiento) {
            // No hay movimiento que revertir
            return;
        }

        // Obtener stock actual
        $inventario = DB::table('inventarios')->where('id', $inventarioId)->first();
        $stockAnterior = $inventario->stock;
        $stockNuevo = $stockAnterior + $cantidad;

        // Obtener motivo de devolución
        $motivo = DB::table('motivos_movimientos')
            ->where('codigo', 'entrada_devolucion_os')
            ->where('activo', 1)
            ->first();

        // Crear movimiento de entrada (reversa)
        DB::table('movimientos_inventario')->insert([
            'inventario_id' => $inventarioId,
            'tipo_movimiento' => 'entrada',
            'cantidad' => $cantidad,
            'stock_anterior' => $stockAnterior,
            'stock_nuevo' => $stockNuevo,
            'costo_unitario' => $movimiento->costo_unitario,
            'entrada_id' => null,
            'venta_id' => null,
            'orden_servicio_id' => null,
            'traslado_id' => null,
            'ajuste_id' => null,
            'costo_adicional_id' => $costoAdicionalId,
            'motivo_id' => $motivo->id,
            'documento_referencia' => null,
            'observaciones' => "Reversa: Eliminación de costo adicional #{$costoAdicionalId}",
            'usuario_id' => auth()->id() ?? 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Actualizar stock en inventarios
        DB::table('inventarios')
            ->where('id', $inventarioId)
            ->update([
                'stock' => $stockNuevo,
                'updated_at' => now(),
            ]);
    }
    
    /**
     * Registrar historial de costos con desglose detallado
     */
    private function registrarHistorialCostos($entradaId)
    {
        // Obtener entrada
        $entrada = DB::table('entradas_producto')->where('id', $entradaId)->first();
        
        // Solo aplicar para entradas tipo cliente
        if (!$entrada || $entrada->tipo_entrada !== 'cliente') {
            return;
        }

        // Obtener items de la entrada
        $items = DB::table('entradas_producto_items')
            ->where('entrada_id', $entradaId)
            ->get();

        if ($items->isEmpty()) {
            return;
        }

        // Obtener costos adicionales con detalle
        $repuestosInventario = DB::table('entradas_costos_repuestos_inventario as ecri')
            ->select([
                'ecri.*',
                'i.nombre as inventario_nombre',
                'i.codigo as inventario_codigo',
            ])
            ->leftJoin('inventarios as i', 'ecri.inventario_id', '=', 'i.id')
            ->where('ecri.entrada_id', $entradaId)
            ->get();

        $repuestosExternos = DB::table('entradas_costos_repuestos_externos as ecre')
            ->select([
                'ecre.*',
                'p.nombre as proveedor_nombre',
            ])
            ->leftJoin('proveedores as p', 'ecre.proveedor_id', '=', 'p.id')
            ->where('ecre.entrada_id', $entradaId)
            ->get();

        $pagosTecnicos = DB::table('entradas_costos_tecnicos as ect')
            ->select([
                'ect.*',
                'u.name as tecnico_nombre',
            ])
            ->leftJoin('users as u', 'ect.tecnico_id', '=', 'u.id')
            ->where('ect.entrada_id', $entradaId)
            ->get();

        // ✅ Calcular costo base desde items
        $costoBaseCalculado = DB::table('entradas_producto_items')
            ->where('entrada_id', $entradaId)
            ->selectRaw('SUM(costo_unitario * cantidad + IFNULL(costo_flete_asignado, 0)) as total')
            ->value('total') ?? 0;

        // Construir desglose JSON
        $desglose = [
            'costo_base' => [
                'descripcion' => 'Costo de adquisición',
                'monto' => (float)$costoBaseCalculado,
            ],
            'repuestos_inventario' => $repuestosInventario->map(function($r) {
                return [
                    'id' => $r->id,
                    'inventario_id' => $r->inventario_id,
                    'nombre' => $r->inventario_nombre,
                    'codigo' => $r->inventario_codigo,
                    'cantidad' => $r->cantidad,
                    'costo_unitario' => (float)$r->costo_unitario,
                    'costo_total' => (float)$r->costo_total,
                    'observaciones' => $r->observaciones,
                ];
            })->toArray(),
            'repuestos_externos' => $repuestosExternos->map(function($r) {
                return [
                    'id' => $r->id,
                    'descripcion' => $r->descripcion,
                    'cantidad' => $r->cantidad,
                    'costo_unitario' => (float)$r->costo_unitario,
                    'costo_total' => (float)$r->costo_total,
                    'proveedor' => $r->proveedor_nombre,
                    'observaciones' => $r->observaciones,
                ];
            })->toArray(),
            'pagos_tecnicos' => $pagosTecnicos->map(function($p) {
                return [
                    'id' => $p->id,
                    'descripcion' => $p->descripcion,
                    'tipo_pago' => $p->tipo_pago,
                    'valor' => (float)$p->valor,
                    'costo_calculado' => (float)$p->costo_calculado,
                    'tecnico' => $p->tecnico_nombre,
                    'observaciones' => $p->observaciones,
                ];
            })->toArray(),
        ];

        // Calcular totales - usar el mismo valor calculado antes
        $costoRepuestosInventario = $repuestosInventario->sum('costo_total');
        $costoRepuestosExternos = $repuestosExternos->sum('costo_total');
        $costoTecnicos = $pagosTecnicos->sum('costo_calculado');
        $costoTotal = $costoBaseCalculado + $costoRepuestosInventario + $costoRepuestosExternos + $costoTecnicos;

        // Registrar en historial para cada item de inventario
        foreach ($items as $item) {
            // Obtener costo actual del inventario
            $inventario = DB::table('inventarios')->where('id', $item->inventario_id)->first();
            
            if (!$inventario) {
                continue;
            }

            DB::table('inventarios_historial_costos')->insert([
                'inventario_id' => $item->inventario_id,
                'entrada_id' => $entradaId,
                'costo_base' => $item->costo_unitario,
                'costo_flete' => $item->costo_flete_asignado ?? 0,
                'costo_repuestos_inventario' => $costoRepuestosInventario,
                'costo_repuestos_externos' => $costoRepuestosExternos,
                'costo_tecnicos' => $costoTecnicos,
                'costo_total' => $inventario->costo,
                'desglose_json' => json_encode($desglose, JSON_UNESCAPED_UNICODE),
                'motivo' => 'Actualización de costos adicionales',
                'tipo_operacion' => 'costo_adicional_agregado',
                'usuario_id' => auth()->id() ?? 1,
                'created_at' => now(),
            ]);
        }
    }

    // ============================================
    // 🔮 MÉTODOS PREPARADOS PARA MÓDULO DE GASTOS
    // ============================================
    
    /**
     * INSTRUCCIONES PARA ACTIVAR EN EL FUTURO:
     * 
     * 1. Ejecuta el script: preparar_campos_gastos.sql
     * 2. Implementa el módulo de gastos con la tabla 'gastos'
     * 3. Descomenta los métodos a continuación
     * 4. Llama registrarGasto() desde:
     *    - addRepuestoExterno() después de crear el registro
     *    - addPagoTecnico() después de crear el registro
     * 5. Llama eliminarGasto() desde:
     *    - destroy() cuando se elimina un costo
     */
    
    /*
    private function registrarGasto($tipo, $costoId, $descripcion, $monto, $proveedorId = null, $tecnicoId = null)
    {
        // Verificar que la tabla gastos existe
        if (!Schema::hasTable('gastos')) {
            return null; // Módulo de gastos no implementado aún
        }

        $gastoId = DB::table('gastos')->insertGetId([
            'tipo' => $tipo, // 'repuesto_externo' o 'pago_tecnico'
            'descripcion' => $descripcion,
            'monto' => $monto,
            'fecha' => now()->format('Y-m-d'),
            'categoria_id' => $this->obtenerCategoriaGasto($tipo),
            'proveedor_id' => $proveedorId,
            'usuario_id' => auth()->id() ?? 1,
            'referencia_tipo' => $tipo === 'repuesto_externo' 
                ? 'entradas_costos_repuestos_externos' 
                : 'entradas_costos_tecnicos',
            'referencia_id' => $costoId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Actualizar el registro con el gasto_id
        $tabla = $tipo === 'repuesto_externo' 
            ? 'entradas_costos_repuestos_externos' 
            : 'entradas_costos_tecnicos';

        DB::table($tabla)
            ->where('id', $costoId)
            ->update(['gasto_id' => $gastoId]);

        return $gastoId;
    }

    private function obtenerCategoriaGasto($tipo)
    {
        // Obtener o crear categoría de gasto según tipo
        $nombreCategoria = $tipo === 'repuesto_externo' 
            ? 'Compra de Repuestos' 
            : 'Pagos a Técnicos';

        $categoria = DB::table('categorias_gastos')
            ->where('nombre', $nombreCategoria)
            ->first();

        if ($categoria) {
            return $categoria->id;
        }

        // Crear categoría si no existe
        return DB::table('categorias_gastos')->insertGetId([
            'nombre' => $nombreCategoria,
            'activo' => 1,
            'created_at' => now(),
        ]);
    }

    private function eliminarGasto($gastoId)
    {
        if (!Schema::hasTable('gastos') || !$gastoId) {
            return;
        }

        DB::table('gastos')->where('id', $gastoId)->delete();
    }

    // EJEMPLO DE CÓMO LLAMAR (Descomenta cuando implementes gastos):
    // 
    // En addRepuestoExterno(), después de insertGetId:
    // $this->registrarGasto(
    //     'repuesto_externo',
    //     $costoId,
    //     $request->descripcion,
    //     $costoTotal,
    //     $request->proveedor_id
    // );
    //
    // En addPagoTecnico(), después de insertGetId:
    // $this->registrarGasto(
    //     'pago_tecnico',
    //     $pagoId,
    //     $request->descripcion,
    //     $costoCalculado,
    //     null,
    //     $request->tecnico_id
    // );
    //
    // En destroy(), antes de delete:
    // if ($tipo === 'repuesto-externo' || $tipo === 'pago-tecnico') {
    //     $registro = DB::table($tabla)->where('id', $id)->first();
    //     if ($registro && $registro->gasto_id) {
    //         $this->eliminarGasto($registro->gasto_id);
    //     }
    // }
    */
}