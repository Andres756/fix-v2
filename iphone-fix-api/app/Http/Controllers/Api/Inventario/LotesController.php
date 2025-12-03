<?php
// app/Http/Controllers/Api/Inventario/LotesController.php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LotesController extends Controller
{
    /**
     * Listar lotes con paginación y filtros
     */
    public function index(Request $request)
    {
        $query = DB::table('lotes')
            ->select([
                'lotes.*',
                'proveedores.nombre as proveedor_nombre',
                'proveedores.nit as proveedor_nit',
            ])
            ->leftJoin('proveedores', 'lotes.proveedor_id', '=', 'proveedores.id');

        // Filtros
        if ($request->has('proveedor_id')) {
            $query->where('lotes.proveedor_id', $request->proveedor_id);
        }

        if ($request->has('fecha_desde')) {
            $query->where('lotes.fecha_ingreso', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta')) {
            $query->where('lotes.fecha_ingreso', '<=', $request->fecha_hasta);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('lotes.numero_lote', 'like', "%{$search}%")
                  ->orWhere('proveedores.nombre', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $lotes = $query->orderBy('lotes.fecha_ingreso', 'desc')
                      ->orderBy('lotes.id', 'desc')
                      ->paginate($perPage);

        // Formatear los datos
        $lotes->getCollection()->transform(function ($lote) {
            return [
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
        });

        return response()->json($lotes);
    }

    /**
     * Obtener lotes para selector (sin paginación)
     */
    public function options(Request $request)
    {
        $query = DB::table('lotes')
            ->select([
                'lotes.*',
                'proveedores.nombre as proveedor_nombre',
                'proveedores.nit as proveedor_nit',
            ])
            ->leftJoin('proveedores', 'lotes.proveedor_id', '=', 'proveedores.id');

        if ($request->has('proveedor_id')) {
            $query->where('lotes.proveedor_id', $request->proveedor_id);
        }

        $lotes = $query->orderBy('lotes.fecha_ingreso', 'desc')
                      ->orderBy('lotes.numero_lote', 'desc')
                      ->get()
                      ->map(function ($lote) {
                          return [
                              'id' => $lote->id,
                              'numero_lote' => $lote->numero_lote,
                              'proveedor_id' => $lote->proveedor_id,
                              'costo_flete' => $lote->costo_flete,
                              'fecha_ingreso' => $lote->fecha_ingreso,
                              'notas' => $lote->notas,
                              'proveedor' => $lote->proveedor_id ? [
                                  'id' => $lote->proveedor_id,
                                  'nombre' => $lote->proveedor_nombre,
                                  'nit' => $lote->proveedor_nit,
                              ] : null,
                          ];
                      });

        return response()->json(['data' => $lotes]);
    }

    /**
     * Crear un nuevo lote
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numero_lote' => 'required|string|max:100|unique:lotes,numero_lote',
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
            $loteId = DB::table('lotes')->insertGetId([
                'numero_lote' => $request->numero_lote,
                'proveedor_id' => $request->proveedor_id,
                'costo_flete' => $request->costo_flete ?? 0,
                'fecha_ingreso' => $request->fecha_ingreso ?? now()->toDateString(),
                'notas' => $request->notas,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

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

            $updateData = [];

            if ($request->has('numero_lote')) {
                $updateData['numero_lote'] = $request->numero_lote;
            }

            if ($request->has('proveedor_id')) {
                $updateData['proveedor_id'] = $request->proveedor_id;
            }

            if ($request->has('costo_flete')) {
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
                    'message' => "No se puede eliminar el lote porque tiene {$entradasCount} entradas asociadas",
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