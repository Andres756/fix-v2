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

            $costoBase = $entrada->total_entrada ?? 0;
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

            // TODO: Crear salida de inventario automática (uso interno)
            // Esto lo implementaremos en el siguiente paso
            
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

            $costoTotal = $request->costo_unitario * $request->cantidad;

            // Crear registro
            $costoId = DB::table('entradas_costos_repuestos_externos')->insertGetId([
                'entrada_id' => $entradaId,
                'proveedor_id' => $request->proveedor_id,
                'descripcion' => $request->descripcion,
                'cantidad' => $request->cantidad,
                'costo_unitario' => $request->costo_unitario,
                'costo_total' => $costoTotal,
                'observaciones' => $request->observaciones,
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
                ])
                ->leftJoin('proveedores as p', 'ecre.proveedor_id', '=', 'p.id')
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
                $costoBase = $entrada->total_entrada ?? 0;
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

            // TODO: Si es repuesto de inventario, revertir la salida

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

        // Obtener costo base
        $entrada = DB::table('entradas_producto')->where('id', $entradaId)->first();
        $costoBase = $entrada->total_entrada ?? 0;
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
    }
}