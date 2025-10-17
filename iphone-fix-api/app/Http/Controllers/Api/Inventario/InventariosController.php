<?php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\Inventario;
use App\Http\Resources\Inventario\InventarioResource;
use App\Http\Requests\Inventario\Inventarios\StoreInventarioRequest;
use App\Http\Requests\Inventario\Inventarios\UpdateInventarioRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;

class InventariosController extends Controller
{
    public function index(Request $r)
    {
        $perPage = max(1, min((int) $r->input('per_page', 15), 100));
        $allowedSort = ['updated_at', 'nombre', 'codigo', 'precio', 'stock'];
        $sortBy  = in_array($r->input('sort_by'), $allowedSort, true) ? $r->input('sort_by') : 'updated_at';
        $sortDir = strtolower($r->input('sort_dir')) === 'asc' ? 'asc' : 'desc';

        $q = Inventario::query();

        // 🔍 Filtros de búsqueda
        if ($r->filled('q')) {
            $search = $r->input('q');
            $q->where(function($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                      ->orWhere('codigo', 'like', "%{$search}%")
                      ->orWhere('nombre_detallado', 'like', "%{$search}%");
            });
        }

        if ($r->filled('tipo_inventario_id')) $q->where('tipo_inventario_id', $r->input('tipo_inventario_id'));
        if ($r->filled('categoria_id'))       $q->where('categoria_id', $r->input('categoria_id'));
        if ($r->filled('estado_inventario_id')) $q->where('estado_inventario_id', $r->input('estado_inventario_id'));

        $q->orderBy($sortBy, $sortDir);

        return InventarioResource::collection(
            $q->with(['categoria:id,nombre', 'estado:id,nombre', 'tipo:id,nombre'])
              ->paginate($perPage)
        );
    }

    public function store(StoreInventarioRequest $r)
    {
        try {
            $data = $r->validated();

            // ⚙️ Valores iniciales
            $data['stock'] = 0;
            $data['costo'] = 0;
            $data['estado_inventario_id'] = 2; // SIN STOCK

            // Si es equipo, stock mínimo = 1
            if ((int)($data['tipo_inventario_id'] ?? 0) === $this->tipoEquiposId()) {
                $data['stock_minimo'] = 1;
            }

            // 🔒 Asegurar nombre_detallado no nulo
            $data['nombre_detallado'] = $r->input('nombre_detallado', $data['nombre'] ?? '');
            if (empty($data['nombre_detallado'])) {
                return response()->json(['message' => 'El nombre detallado es obligatorio.'], 422);
            }

            unset($data['imagen'], $data['detalle_equipo'], $data['detalle_producto'], $data['detalle_repuesto']);

            $inv = DB::transaction(function () use ($r, $data) {
                $inv = Inventario::create($data);

                // 🔧 Crear detalles por tipo
                if ($r->filled('detalle_equipo'))   $inv->detalleEquipo()->create($r->input('detalle_equipo'));
                if ($r->filled('detalle_producto')) $inv->detalleProducto()->create($r->input('detalle_producto'));
                if ($r->filled('detalle_repuesto')) $inv->detalleRepuesto()->create($r->input('detalle_repuesto'));

                // 📷 Imagen
                if ($r->hasFile('imagen')) {
                    $path = $r->file('imagen')->store('inventarios', 'public');
                    $inv->ruta_imagen = $path;
                    $inv->save();
                }

                return $inv;
            });

            return (new InventarioResource(
                $inv->load(['categoria:id,nombre', 'estado:id,nombre', 'tipo:id,nombre',
                            'detalleEquipo', 'detalleProducto', 'detalleRepuesto'])
            ))->response()->setStatusCode(Response::HTTP_CREATED);

        } catch (QueryException $e) {
            // ⚠️ Captura errores de duplicidad
            if ($e->errorInfo[1] === 1062) {
                if (str_contains($e->getMessage(), 'inventarios_nombre_detallado_unique')) {
                    return response()->json(['message' => 'El nombre detallado ya existe.'], 422);
                }
                if (str_contains($e->getMessage(), 'inventarios_codigo_unique')) {
                    return response()->json(['message' => 'El código ya existe.'], 422);
                }
            }

            // Otros errores SQL
            return response()->json([
                'message' => 'Error al registrar el inventario.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Inventario $inventario)
    {
        return new InventarioResource(
            $inventario->load(['categoria:id,nombre', 'estado:id,nombre', 'tipo:id,nombre',
                               'detalleEquipo', 'detalleProducto', 'detalleRepuesto'])
        );
    }

    public function update(UpdateInventarioRequest $r, Inventario $inventario)
    {
        try {
            $data = $r->validated();

            unset($data['stock'], $data['costo'], $data['estado_inventario_id'], $data['imagen']);

            if ((int)($data['tipo_inventario_id'] ?? $inventario->tipo_inventario_id) === $this->tipoEquiposId()) {
                $data['stock_minimo'] = 1;
            }

            $data['nombre_detallado'] = $r->input('nombre_detallado', $data['nombre'] ?? $inventario->nombre ?? '');
            if (empty($data['nombre_detallado'])) {
                return response()->json(['message' => 'El nombre detallado es obligatorio.'], 422);
            }

            DB::transaction(function () use ($r, $inventario, $data) {
                $inventario->update($data);

                if ($r->has('detalle_equipo'))
                    $inventario->detalleEquipo()->updateOrCreate([], $r->input('detalle_equipo', []));

                if ($r->has('detalle_producto'))
                    $inventario->detalleProducto()->updateOrCreate([], $r->input('detalle_producto', []));

                if ($r->has('detalle_repuesto'))
                    $inventario->detalleRepuesto()->updateOrCreate([], $r->input('detalle_repuesto', []));

                if ($r->hasFile('imagen')) {
                    if ($inventario->ruta_imagen)
                        Storage::disk('public')->delete($inventario->ruta_imagen);

                    $path = $r->file('imagen')->store('inventarios', 'public');
                    $inventario->ruta_imagen = $path;
                    $inventario->save();
                }
            });

            return new InventarioResource(
                $inventario->fresh()->load(['categoria:id,nombre', 'estado:id,nombre', 'tipo:id,nombre',
                                            'detalleEquipo', 'detalleProducto', 'detalleRepuesto'])
            );

        } catch (QueryException $e) {
            if ($e->errorInfo[1] === 1062) {
                if (str_contains($e->getMessage(), 'inventarios_nombre_detallado_unique')) {
                    return response()->json(['message' => 'El nombre detallado ya existe.'], 422);
                }
                if (str_contains($e->getMessage(), 'inventarios_codigo_unique')) {
                    return response()->json(['message' => 'El código ya existe.'], 422);
                }
            }

            return response()->json([
                'message' => 'Error al actualizar el inventario.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Inventario $inventario)
    {
        if ($inventario->ruta_imagen)
            Storage::disk('public')->delete($inventario->ruta_imagen);

        $inventario->delete();

        return response()->noContent();
    }

    /** Obtiene (y cachea) el id del tipo EQUIPOS */
    private function tipoEquiposId(): int
    {
        static $cache = null;
        if ($cache !== null) return $cache;

        $id = DB::table('tipos_de_inventario')
            ->whereRaw('UPPER(nombre) = ?', ['EQUIPOS'])
            ->value('id');

        return $cache = (int) ($id ?? -1);
    }

    /** Búsqueda de repuestos para orden de servicio */
    public function searchRepuestos(Request $r)
    {
        $search = $r->input('q', '');
        $perPage = min((int) $r->input('per_page', 15), 50);

        $repuestos = Inventario::where('tipo_inventario_id', 3)
            ->where('stock', '>', 0)
            ->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%")
                  ->orWhere('nombre_detallado', 'like', "%{$search}%");
            })
            ->with(['categoria:id,nombre', 'estado:id,nombre'])
            ->paginate($perPage);

        return InventarioResource::collection($repuestos);
    }
}
