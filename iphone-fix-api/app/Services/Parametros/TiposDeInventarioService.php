<?php
// app/Services/Parametros/TiposDeInventarioService.php
namespace App\Services\Parametros;

use App\Models\Parametros\TipoDeInventario;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TiposDeInventarioService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $q = TipoDeInventario::query()
            ->buscar($filters['search'] ?? null)
            ->orderBy($filters['sort_by'] ?? 'nombre', $filters['sort_dir'] ?? 'asc');

        return $q->paginate((int)($filters['per_page'] ?? 15));
    }

    public function create(array $data): TipoDeInventario
    {
        return TipoDeInventario::create($data);
    }

    public function update(TipoDeInventario $m, array $data): TipoDeInventario
    {
        $m->fill($data)->save();
        return $m;
    }

    public function options()
    {
        return TipoDeInventario::query()
            ->orderBy('nombre')
            ->get(['id','nombre']);
    }
}
