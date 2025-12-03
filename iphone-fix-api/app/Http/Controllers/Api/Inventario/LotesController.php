<?php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LotesController extends Controller
{
    /**
     * Listar todos los lotes con paginación y filtros
     */
    public function index(Request $request)
    {
        $query = DB::table('lotes')
            ->select([
                'lotes.*',
                'proveedores.nombre as proveedor_nombre',
                'proveedores.nit as proveedor_nit',
                DB::raw('(SELECT COUNT(*) FROM entradas_producto WHERE entradas_producto.lote_id = lotes.id) as entradas_count')
            ])
            ->leftJoin('proveedores', 'lotes.proveedor_id', '=', 'proveedores.id');

        // ✅ NUEVO: Filtro por disponibilidad usando campo usado
        if ($request->has('disponibles') && $request->disponibles == 'true') {
            $query->where('lotes.usado', false);
        }

        // Filtro por estado de uso (usado/sin usar)
        if ($request->has('usado')) {
            $query->where('lotes.usado', $request->usado === 'true');
        }

        // Filtros existentes
        if ($request->has('proveedor_id')) {
            $query->where('lotes.proveedor_id', $request->proveedor_id);
        }

        if ($request->has('fecha_desde')) {
            $query->whereDate('lotes.fecha_ingreso', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta')) {
            $query->whereDate('lotes.fecha_ingreso', '<=', $request->fecha_hasta);
        }

        $perPage = $request->get('per_page', 15);
        $lotes = $query->orderBy('lotes.fecha_ingreso', 'desc')
                       ->orderBy('lotes.id', 'desc')
                       ->paginate($perPage);

        // ✅ Formato consistente con otros endpoints
        return response()->json([
            'data' => $lotes->items(),
            'pagination' => [
                'current_page' => $lotes->currentPage(),
                'last_page' => $lotes->lastPage(),
                'per_page' => $lotes->perPage(),
                'total' => $lotes->total(),
                'from' => $lotes->firstItem(),
                'to' => $lotes->lastItem(),
            ]
        ]);
    }

    /**
     * Listar lotes para selector (sin paginación)
     * ✅ MODIFICADO: Solo muestra lotes que NO han sido usados
     * ✅ NUEVO: Si se pasa include_lote_id, incluye ese lote aunque esté usado
     */
    public function options(Request $request)
    {
        $query = DB::table('lotes')
            ->select([
                'lotes.id',
                'lotes.numero_lote',
                'lotes.proveedor_id',
                'lotes.costo_flete',
                'lotes.fecha_ingreso',
                'lotes.usado',
                'proveedores.nombre as proveedor_nombre',
            ])
            ->leftJoin('proveedores', 'lotes.proveedor_id', '=', 'proveedores.id');

        // ✅ NUEVO: Si se especifica un lote a incluir, agregarlo a la consulta
        $includeLoteId = $request->get('include_lote_id');
        
        if ($includeLoteId) {
            // Incluir lotes no usados O el lote específico
            $query->where(function($q) use ($includeLoteId) {
                $q->where('lotes.usado', false)
                  ->orWhere('lotes.id', $includeLoteId);
            });
        } else {
            // Solo lotes no usados
            $query->where('lotes.usado', false);
        }

        // Filtro opcional por proveedor
        if ($request->has('proveedor_id')) {
            $query->where('lotes.proveedor_id', $request->proveedor_id);
        }

        $lotes = $query->orderBy('lotes.fecha_ingreso', 'desc')
                       ->get();

        return response()->json($lotes);
    }

    /**
     * Crear un nuevo lote
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numero_lote' => 'required|string|max:100|unique:lotes,numero_lote',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'costo_flete' => 'required|numeric|min:0',
            'fecha_ingreso' => 'required|date',
            'notas' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $loteId = DB::table('lotes')->insertGetId([
                'numero_lote' => $request->numero_lote,
                'proveedor_id' => $request->proveedor_id,
                'costo_flete' => $request->costo_flete,
                'fecha_ingreso' => $request->fecha_ingreso,
                'notas' => $request->notas,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Obtener el lote creado con datos del proveedor
            $lote = DB::table('lotes')
                ->select([
                    'lotes.*',
                    'proveedores.nombre as proveedor_nombre',
                    'proveedores.nit as proveedor_nit',
                ])
                ->leftJoin('proveedores', 'lotes.proveedor_id', '=', 'proveedores.id')
                ->where('lotes.id', $loteId)
                ->first();

            $loteFormatted = [
                'id' => $lote->id,
                'numero_lote' => $lote->numero_lote,
                'proveedor_id' => $lote->proveedor_id,
                'costo_flete' => $lote->costo_flete,
                'fecha_ingreso' => $lote->fecha_ingreso,
                'notas' => $lote->notas,
                'created_at' => $lote->created_at,
                'updated_at' => $lote->updated_at,
                'proveedor' => $lote->proveedor_id ? [
                    'id' => $lote->proveedor_id,
                    'nombre' => $lote->proveedor_nombre,
                    'nit' => $lote->proveedor_nit,
                ] : null,
            ];

            return response()->json([
                'message' => 'Lote creado exitosamente',
                'data' => $loteFormatted
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el lote',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener un lote específico
     */
    public function show($id)
    {
        $lote = DB::table('lotes')
            ->select([
                'lotes.*',
                'proveedores.nombre as proveedor_nombre',
                'proveedores.nit as proveedor_nit',
                DB::raw('(SELECT COUNT(*) FROM entradas_producto WHERE entradas_producto.lote_id = lotes.id) as entradas_count')
            ])
            ->leftJoin('proveedores', 'lotes.proveedor_id', '=', 'proveedores.id')
            ->where('lotes.id', $id)
            ->first();

        if (!$lote) {
            return response()->json([
                'message' => 'Lote no encontrado'
            ], 404);
        }

        $loteFormatted = [
            'id' => $lote->id,
            'numero_lote' => $lote->numero_lote,
            'proveedor_id' => $lote->proveedor_id,
            'costo_flete' => $lote->costo_flete,
            'fecha_ingreso' => $lote->fecha_ingreso,
            'notas' => $lote->notas,
            'usado' => (bool)$lote->usado, // ✅ Campo explícito
            'entradas_count' => $lote->entradas_count, // ✅ Para información adicional
            'created_at' => $lote->created_at,
            'updated_at' => $lote->updated_at,
            'proveedor' => $lote->proveedor_id ? [
                'id' => $lote->proveedor_id,
                'nombre' => $lote->proveedor_nombre,
                'nit' => $lote->proveedor_nit,
            ] : null,
        ];

        return response()->json(['data' => $loteFormatted]);
    }

    /**
     * Actualizar un lote
     * ✅ MODIFICADO: No permite editar costo_flete si el lote ya está marcado como usado
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'numero_lote' => 'sometimes|string|max:100|unique:lotes,numero_lote,' . $id,
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'costo_flete' => 'nullable|numeric|min:0',
            'fecha_ingreso' => 'nullable|date',
            'notas' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $lote = DB::table('lotes')->where('id', $id)->first();

            if (!$lote) {
                return response()->json([
                    'message' => 'Lote no encontrado'
                ], 404);
            }

            // ✅ RESTRICCIÓN: Si el lote está marcado como usado, NO permitir editar costo_flete
            if ($lote->usado && $request->has('costo_flete')) {
                return response()->json([
                    'message' => "No se puede editar el costo de flete porque este lote ya está en uso. El flete ya fue asignado a una o más entradas de inventario.",
                ], 409);
            }

            $updateData = [];

            if ($request->has('numero_lote')) {
                $updateData['numero_lote'] = $request->numero_lote;
            }

            if ($request->has('proveedor_id')) {
                $updateData['proveedor_id'] = $request->proveedor_id;
            }

            // Solo permitir actualizar costo_flete si NO está usado
            if ($request->has('costo_flete') && !$lote->usado) {
                $updateData['costo_flete'] = $request->costo_flete;
            }

            if ($request->has('fecha_ingreso')) {
                $updateData['fecha_ingreso'] = $request->fecha_ingreso;
            }

            if ($request->has('notas')) {
                $updateData['notas'] = $request->notas;
            }

            $updateData['updated_at'] = now();

            DB::table('lotes')->where('id', $id)->update($updateData);

            // Obtener el lote actualizado
            $loteActualizado = DB::table('lotes')
                ->select([
                    'lotes.*',
                    'proveedores.nombre as proveedor_nombre',
                    'proveedores.nit as proveedor_nit',
                ])
                ->leftJoin('proveedores', 'lotes.proveedor_id', '=', 'proveedores.id')
                ->where('lotes.id', $id)
                ->first();

            $loteFormatted = [
                'id' => $loteActualizado->id,
                'numero_lote' => $loteActualizado->numero_lote,
                'proveedor_id' => $loteActualizado->proveedor_id,
                'costo_flete' => $loteActualizado->costo_flete,
                'fecha_ingreso' => $loteActualizado->fecha_ingreso,
                'notas' => $loteActualizado->notas,
                'usado' => (bool)$loteActualizado->usado,
                'created_at' => $loteActualizado->created_at,
                'updated_at' => $loteActualizado->updated_at,
                'proveedor' => $loteActualizado->proveedor_id ? [
                    'id' => $loteActualizado->proveedor_id,
                    'nombre' => $loteActualizado->proveedor_nombre,
                    'nit' => $loteActualizado->proveedor_nit,
                ] : null,
            ];

            return response()->json([
                'message' => 'Lote actualizado exitosamente',
                'data' => $loteFormatted
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el lote',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un lote
     * ✅ YA EXISTÍA: No permite eliminar si tiene entradas asociadas
     */
    public function destroy($id)
    {
        try {
            // Verificar si el lote tiene entradas asociadas
            $entradasCount = DB::table('entradas_producto')
                ->where('lote_id', $id)
                ->count();

            if ($entradasCount > 0) {
                return response()->json([
                    'message' => "No se puede eliminar el lote porque tiene {$entradasCount} entrada(s) asignada(s). Primero desasigne las entradas.",
                ], 409);
            }

            $deleted = DB::table('lotes')->where('id', $id)->delete();

            if (!$deleted) {
                return response()->json([
                    'message' => 'Lote no encontrado'
                ], 404);
            }

            return response()->json([
                'message' => 'Lote eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el lote',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}