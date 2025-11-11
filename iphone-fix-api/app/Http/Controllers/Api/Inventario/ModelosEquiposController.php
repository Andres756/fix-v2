<?php
// app/Http/Controllers/Api/Inventario/ModelosEquiposController.php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\ModeloEquipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModelosEquiposController extends Controller
{
    /**
     * Listar modelos (para selects)
     */
    public function index(Request $request)
    {
        $query = ModeloEquipo::query();

        if ($request->filled('q')) {
            $query->buscar($request->q);
        }

        if ($request->filled('marca')) {
            $query->porMarca($request->marca);
        }

        if ($request->filled('familia')) {
            $query->porFamilia($request->familia);
        }

        if ($request->boolean('activo', true)) {
            $query->activos();
        }

        $modelos = $query->orderBy('marca')
            ->orderBy('familia')
            ->orderBy('nombre')
            ->paginate($request->get('per_page', 50));

        return response()->json($modelos);
    }

    /**
     * Opciones para select (sin paginación)
     */
    public function options(Request $request)
    {
        $query = ModeloEquipo::activos();

        if ($request->filled('familia')) {
            $query->porFamilia($request->familia);
        }

        $modelos = $query->orderBy('nombre')
            ->get(['id', 'nombre', 'marca', 'familia']);

        return response()->json(['data' => $modelos]);
    }

    /**
     * Crear nuevo modelo
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'marca' => 'required|string|max:50',
            'familia' => 'nullable|string|max:50',
            'anio_lanzamiento' => 'nullable|integer|min:2000|max:2100',
            'descripcion' => 'nullable|string',
        ]);

        $modelo = ModeloEquipo::create($validated);

        return response()->json([
            'message' => 'Modelo creado exitosamente',
            'data' => $modelo
        ], 201);
    }

    /**
     * Actualizar modelo
     */
    public function update(Request $request, ModeloEquipo $modeloEquipo)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'marca' => 'sometimes|string|max:50',
            'familia' => 'nullable|string|max:50',
            'anio_lanzamiento' => 'nullable|integer|min:2000|max:2100',
            'descripcion' => 'nullable|string',
            'activo' => 'sometimes|boolean',
        ]);

        $modeloEquipo->update($validated);

        return response()->json([
            'message' => 'Modelo actualizado exitosamente',
            'data' => $modeloEquipo
        ]);
    }

    /**
     * 📊 REPORTE: Equipos agrupados por modelo
     */
    public function reporteInventario(Request $request)
    {
        $query = ModeloEquipo::query()
            ->select([
                'modelos_equipos.id',
                'modelos_equipos.nombre',
                'modelos_equipos.marca',
                'modelos_equipos.familia',
                DB::raw('COUNT(DISTINCT i.id) as cantidad_disponible'),
                DB::raw('COUNT(DISTINCT de.almacenamiento) as variantes_almacenamiento'),
                DB::raw('COUNT(DISTINCT de.color) as variantes_color'),
                DB::raw('MIN(i.costo) as costo_minimo'),
                DB::raw('MAX(i.costo) as costo_maximo'),
                DB::raw('AVG(i.costo) as costo_promedio'),
                DB::raw('MIN(i.precio) as precio_minimo'),
                DB::raw('MAX(i.precio) as precio_maximo'),
                DB::raw('SUM(i.costo * i.stock) as valor_inventario')
            ])
            ->join('detalles_equipos as de', 'modelos_equipos.id', '=', 'de.modelo_equipo_id')
            ->join('inventarios as i', 'de.inventario_id', '=', 'i.id')
            ->where('i.tipo_inventario_id', 1)
            ->where('i.activo', 1)
            ->where('i.stock', '>', 0);

        if ($request->filled('familia')) {
            $query->where('modelos_equipos.familia', $request->familia);
        }

        if ($request->filled('marca')) {
            $query->where('modelos_equipos.marca', $request->marca);
        }

        $resultado = $query->groupBy([
                'modelos_equipos.id',
                'modelos_equipos.nombre',
                'modelos_equipos.marca',
                'modelos_equipos.familia'
            ])
            ->orderBy('cantidad_disponible', 'DESC')
            ->get();

        return response()->json(['data' => $resultado]);
    }

    /**
     * Detalle de equipos de un modelo específico
     */
    public function equiposPorModelo(ModeloEquipo $modeloEquipo)
    {
        $equipos = $modeloEquipo->inventarios()
            ->with(['detalleEquipo', 'categoria', 'estado'])
            ->where('stock', '>', 0)
            ->where('activo', 1)
            ->get();

        return response()->json(['data' => $equipos]);
    }
}