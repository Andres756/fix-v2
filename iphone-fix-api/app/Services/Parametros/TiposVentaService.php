<?php

namespace App\Services\Parametros;

use App\Models\Parametros\TipoVenta;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TiposVentaService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $search   = $filters['search']   ?? null;
        $activo   = $filters['activo']   ?? null;
        $perPage  = (int)($filters['per_page'] ?? 15);
        $sortBy   = $filters['sort_by']  ?? 'nombre';
        $sortDir  = $filters['sort_dir'] ?? 'asc';

        $q = TipoVenta::query()
            ->buscar($search)
            ->when(!is_null($activo), fn($qq) => $qq->where('activo', (int)$activo))
            ->orderBy($sortBy, $sortDir);

        return $q->paginate($perPage);
    }

    public function create(array $data): TipoVenta
    {
        $data['activo'] = $data['activo'] ?? true;
        return TipoVenta::create($data);
    }

    public function update(TipoVenta $tv, array $data): TipoVenta
    {
        $tv->fill($data)->save();
        return $tv;
    }

    public function toggle(int $id): TipoVenta
    {
        $tv = TipoVenta::findOrFail($id);
        $tv->activo = !$tv->activo;
        $tv->save();
        return $tv;
    }

    public function deactivate(int $id): void
    {
        $tv = TipoVenta::findOrFail($id);
        $tv->activo = 0;
        $tv->save();
    }

    public function options(?bool $soloActivos = true): Collection
    {
        $q = TipoVenta::query()->orderBy('nombre');
        if ($soloActivos) $q->activos();
        return $q->get(['id','nombre']);
    }
}
