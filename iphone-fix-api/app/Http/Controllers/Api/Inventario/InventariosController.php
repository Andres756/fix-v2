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

        $q = Inventario::query()
            ->with([
                'categoria:id,nombre',
                'proveedor:id,nombre',
                'lote:id,codigo_lote',
                'estado:id,nombre',   // nombre de relación correcto en el Modelo
                'tipo:id,nombre',     // idem
            ])
            ->when($r->filled('tipo_inventario_id'), fn ($qq) =>
                $qq->where('tipo_inventario_id', (int) $r->input('tipo_inventario_id'))
            )
            ->when($r->filled('categoria_id'), fn ($qq) =>
                $qq->where('categoria_id', (int) $r->input('categoria_id'))
            )
            ->when($r->filled('estado_inventario_id'), fn ($qq) =>
                $qq->where('estado_inventario_id', (int) $r->input('estado_inventario_id'))
            )
            ->when($r->filled('q'), function ($qq) use ($r) {
                $s = $r->input('q');
                $qq->where(function ($w) use ($s) {
                    $w->where('nombre', 'like', "%{$s}%")
                      ->orWhere('codigo', 'like', "%{$s}%");
                });
            })
            ->orderBy($sortBy, $sortDir);

        return InventarioResource::collection($q->paginate($perPage));
    }

        public function searchRepuestos(Request $r)
    {
        $s = $r->input('q', '');

        $q = Inventario::query()
            ->select('id', 'codigo', 'nombre') // 👈 solo lo necesario
            ->where('tipo_inventario_id', 3)   // 👈 filtro fijo para repuestos
            ->when($s !== '', function ($qq) use ($s) {
                $qq->where(function ($w) use ($s) {
                    $w->where('nombre', 'like', "%{$s}%")
                    ->orWhere('codigo', 'like', "%{$s}%");
                });
            })
            ->orderBy('nombre', 'asc')
            ->limit(15) // 👈 máximo 15 resultados para autocompletar
            ->get();

        return response()->json($q);
    }

    public function store(StoreInventarioRequest $r)
    {
        $data = $r->validated();

        // Forzar EQUIPOS
        if ((int)($data['tipo_inventario_id'] ?? 0) === $this->tipoEquiposId()) {
            $data['stock'] = 1;
            $data['stock_minimo'] = 1;
        }

        unset($data['imagen'], $data['detalle_equipo'], $data['detalle_producto'], $data['detalle_repuesto']);

        // 🔧 Fallbacks IMPORTANTES
        $data['nombre_detallado'] = $r->input('nombre_detallado', $data['nombre'] ?? '');
        $data['costo_mayor']      = $r->input('costo_mayor', null);

        $inv = DB::transaction(function () use ($r, $data) {
            $inv = Inventario::create($data);

            // Detalles…
            if ($r->filled('detalle_equipo'))   $inv->detalleEquipo()->create($r->input('detalle_equipo'));
            if ($r->filled('detalle_producto')) $inv->detalleProducto()->create($r->input('detalle_producto'));
            if ($r->filled('detalle_repuesto')) $inv->detalleRepuesto()->create($r->input('detalle_repuesto'));

            // Imagen (opcional)
            if ($r->hasFile('imagen')) {
                $path = $r->file('imagen')->store('inventarios', 'public');
                $inv->ruta_imagen = $path;
                $inv->save();
            }

            return $inv;
        });

        return (new InventarioResource(
            $inv->load(['categoria:id,nombre','proveedor:id,nombre','lote:id,codigo_lote','estado:id,nombre','tipo:id,nombre','detalleEquipo','detalleProducto','detalleRepuesto'])
        ))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Inventario $inventario)
    {
        return new InventarioResource(
            $inventario->load([
                'categoria:id,nombre','proveedor:id,nombre','lote:id,codigo_lote',
                'estado:id,nombre','tipo:id,nombre',
                'detalleEquipo','detalleProducto','detalleRepuesto'
            ])
        );
    }

    public function update(UpdateInventarioRequest $r, Inventario $inventario)
    {
        $data = $r->validated();

        if ((int)($data['tipo_inventario_id'] ?? $inventario->tipo_inventario_id) === $this->tipoEquiposId()) {
            $data['stock'] = 1;
            $data['stock_minimo'] = 1;
        }

        unset($data['imagen']);

        // 🔧 Fallbacks IMPORTANTES
        $data['nombre_detallado'] = $r->input('nombre_detallado', $data['nombre'] ?? $inventario->nombre ?? '');
        $data['costo_mayor']      = $r->input('costo_mayor', $inventario->costo_mayor);

        DB::transaction(function () use ($r, $inventario, $data) {
            $inventario->update($data);

            // Upserts de detalles…
            if ($r->has('detalle_equipo'))   $inventario->detalleEquipo()->updateOrCreate([], $r->input('detalle_equipo', []));
            if ($r->has('detalle_producto')) $inventario->detalleProducto()->updateOrCreate([], $r->input('detalle_producto', []));
            if ($r->has('detalle_repuesto')) $inventario->detalleRepuesto()->updateOrCreate([], $r->input('detalle_repuesto', []));

            // Reemplazo de imagen
            if ($r->hasFile('imagen')) {
                if ($inventario->ruta_imagen) Storage::disk('public')->delete($inventario->ruta_imagen);
                $path = $r->file('imagen')->store('inventarios', 'public');
                $inventario->ruta_imagen = $path;
                $inventario->save();
            }
        });

        return new InventarioResource(
            $inventario->load(['categoria:id,nombre','proveedor:id,nombre','lote:id,codigo_lote','estado:id,nombre','tipo:id,nombre','detalleEquipo','detalleProducto','detalleRepuesto'])
        );
    }

    public function destroy(Inventario $inventario)
    {
        // (opcional) borrar imagen del disco
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
}
