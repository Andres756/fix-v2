<?php
// app/Http/Controllers/Api/Inventario/InventariosController.php

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

class InventariosController extends Controller
{
    public function index(Request $r)
    {
        $perPage = max(1, min((int) $r->input('per_page', 15), 100));

        $allowedSort = ['updated_at', 'nombre', 'codigo', 'precio', 'stock'];
        $sortBy  = in_array($r->input('sort_by'), $allowedSort, true) ? $r->input('sort_by') : 'updated_at';
        $sortDir = strtolower($r->input('sort_dir')) === 'asc' ? 'asc' : 'desc';

        $q = Inventario::query();

        // Filtros
        if ($r->filled('q')) {
            $search = $r->input('q');
            $q->where(function($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                      ->orWhere('codigo', 'like', "%{$search}%");
            });
        }

        if ($r->filled('tipo_inventario_id')) {
            $q->where('tipo_inventario_id', $r->input('tipo_inventario_id'));
        }

        if ($r->filled('categoria_id')) {
            $q->where('categoria_id', $r->input('categoria_id'));
        }

        if ($r->filled('estado_inventario_id')) {
            $q->where('estado_inventario_id', $r->input('estado_inventario_id'));
        }

        $q->orderBy($sortBy, $sortDir);

        return InventarioResource::collection(
            $q->with(['categoria:id,nombre', 'estado:id,nombre', 'tipo:id,nombre'])
              ->paginate($perPage)
        );
    }

    public function store(StoreInventarioRequest $r)
    {
        $data = $r->validated();

        // FORZAR VALORES INICIALES según nueva lógica
        $data['stock'] = 0;                    // Siempre inicia en 0
        $data['costo'] = 0;                    // Siempre inicia en 0
        $data['estado_inventario_id'] = 2;    // SIN STOCK (id=2)

        // Validación especial para EQUIPOS
        if ((int)($data['tipo_inventario_id'] ?? 0) === $this->tipoEquiposId()) {
            $data['stock_minimo'] = 1;
        }

        unset($data['imagen'], $data['detalle_equipo'], $data['detalle_producto'], $data['detalle_repuesto']);

        // Fallbacks
        $data['nombre_detallado'] = $r->input('nombre_detallado', $data['nombre'] ?? '');

        $inv = DB::transaction(function () use ($r, $data) {
            $inv = Inventario::create($data);

            // Detalles por tipo
            if ($r->filled('detalle_equipo'))   
                $inv->detalleEquipo()->create($r->input('detalle_equipo'));
            
            if ($r->filled('detalle_producto')) 
                $inv->detalleProducto()->create($r->input('detalle_producto'));
            
            if ($r->filled('detalle_repuesto')) 
                $inv->detalleRepuesto()->create($r->input('detalle_repuesto'));

            // Imagen (opcional)
            if ($r->hasFile('imagen')) {
                $path = $r->file('imagen')->store('inventarios', 'public');
                $inv->ruta_imagen = $path;
                $inv->save();
            }

            return $inv;
        });

        return (new InventarioResource(
            $inv->load([
                'categoria:id,nombre',
                'estado:id,nombre',
                'tipo:id,nombre',
                'detalleEquipo',
                'detalleProducto',
                'detalleRepuesto'
            ])
        ))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Inventario $inventario)
    {
        return new InventarioResource(
            $inventario->load([
                'categoria:id,nombre',
                'estado:id,nombre',
                'tipo:id,nombre',
                'detalleEquipo',
                'detalleProducto',
                'detalleRepuesto'
            ])
        );
    }

    public function update(UpdateInventarioRequest $r, Inventario $inventario)
    {
        $data = $r->validated();

        // NO permitir cambiar stock, costo o estado manualmente
        unset($data['stock'], $data['costo'], $data['estado_inventario_id']);

        // Validación especial para EQUIPOS
        if ((int)($data['tipo_inventario_id'] ?? $inventario->tipo_inventario_id) === $this->tipoEquiposId()) {
            $data['stock_minimo'] = 1;
        }

        unset($data['imagen']);

        // Fallbacks
        $data['nombre_detallado'] = $r->input('nombre_detallado', $data['nombre'] ?? $inventario->nombre ?? '');

        DB::transaction(function () use ($r, $inventario, $data) {
            $inventario->update($data);

            // Upserts de detalles
            if ($r->has('detalle_equipo'))   
                $inventario->detalleEquipo()->updateOrCreate([], $r->input('detalle_equipo', []));
            
            if ($r->has('detalle_producto')) 
                $inventario->detalleProducto()->updateOrCreate([], $r->input('detalle_producto', []));
            
            if ($r->has('detalle_repuesto')) 
                $inventario->detalleRepuesto()->updateOrCreate([], $r->input('detalle_repuesto', []));

            // Reemplazo de imagen
            if ($r->hasFile('imagen')) {
                if ($inventario->ruta_imagen) 
                    Storage::disk('public')->delete($inventario->ruta_imagen);
                
                $path = $r->file('imagen')->store('inventarios', 'public');
                $inventario->ruta_imagen = $path;
                $inventario->save();
            }
        });

        return new InventarioResource(
            $inventario->fresh()->load([
                'categoria:id,nombre',
                'estado:id,nombre',
                'tipo:id,nombre',
                'detalleEquipo',
                'detalleProducto',
                'detalleRepuesto'
            ])
        );
    }

    public function destroy(Inventario $inventario)
    {
        // Borrar imagen del disco
        if ($inventario->ruta_imagen) {
            Storage::disk('public')->delete($inventario->ruta_imagen);
        }
        
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

        $repuestos = Inventario::where('tipo_inventario_id', 3) // Tipo REPUESTO
            ->where('stock', '>', 0) // Solo con stock
            ->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%");
            })
            ->with(['categoria:id,nombre', 'estado:id,nombre'])
            ->paginate($perPage);

        return InventarioResource::collection($repuestos);
    }
}