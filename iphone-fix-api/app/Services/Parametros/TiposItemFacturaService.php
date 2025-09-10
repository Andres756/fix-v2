<?php
// app/Services/Parametros/TiposItemFacturaService.php
namespace App\Services\Parametros;

use App\Models\Parametros\TipoItemFactura;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TiposItemFacturaService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $q = TipoItemFactura::query()
            ->buscar($filters['search'] ?? null)
            ->when(isset($filters['activo']), fn($qq) => $qq->where('activo', (int)$filters['activo']))
            ->orderBy($filters['sort_by'] ?? 'nombre', $filters['sort_dir'] ?? 'asc');

        return $q->paginate((int)($filters['per_page'] ?? 15));
    }

    public function create(array $data): TipoItemFactura
    {
        $data['activo'] = $data['activo'] ?? true;
        return TipoItemFactura::create($data);
    }

    public function update(TipoItemFactura $m, array $data): TipoItemFactura
    {
        $m->fill($data)->save();
        return $m;
    }

    public function toggle(int $id): TipoItemFactura
    {
        $m = TipoItemFactura::findOrFail($id);
        $m->activo = !$m->activo;
        $m->save();
        return $m;
    }

    public function deactivate(int $id): void
    {
        $m = TipoItemFactura::findOrFail($id);
        $m->activo = 0;
        $m->save();
    }

    public function options(bool $soloActivos = true): Collection
    {
        $q = TipoItemFactura::query()->orderBy('nombre');
        if ($soloActivos) $q->where('activo', 1);
        return $q->get(['id','nombre']);
    }
}
