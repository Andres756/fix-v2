<?php
// app/Http/Controllers/Api/Inventario/CategoriasController.php
namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\Categoria;
use App\Http\Resources\Inventario\CategoriaResource;
use App\Http\Requests\Inventario\Categorias\StoreCategoriaRequest;
use App\Http\Requests\Inventario\Categorias\UpdateCategoriaRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoriasController extends Controller
{
    public function index(Request $r)
    {
        // per_page (límite alto para combos)
        $per = (int) $r->input('per_page', 15);
        $per = $per < 1 ? 15 : min($per, 1000);

        $q = Categoria::query()
            ->when($r->filled('tipo_inventario_id'), function ($qq) use ($r) {
                $qq->where('tipo_inventario_id', (int) $r->input('tipo_inventario_id'));
            })
            ->when($r->filled('q'), function ($qq) use ($r) {
                $s = $r->input('q');
                $qq->where('nombre', 'like', "%{$s}%");
            })
            ->orderBy('nombre');

        return CategoriaResource::collection($q->paginate($per));
    }
    public function store(StoreCategoriaRequest $r){
        $x = Categoria::create($r->validated());
        return (new CategoriaResource($x))->response()->setStatusCode(Response::HTTP_CREATED);
    }
    public function show(Categoria $categoria){
        return new CategoriaResource($categoria);
    }
    public function update(UpdateCategoriaRequest $r, Categoria $categoria){
        $categoria->update($r->validated());
        return new CategoriaResource($categoria);
    }
    public function destroy(Categoria $categoria){
        $categoria->delete();
        return response()->noContent();
    }
}
