<?php

namespace App\Services\Parametros;

use App\Models\Parametros\TipoDescuento;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TiposDescuentoService
{
    public function list(array $filters=[]): LengthAwarePaginator
    {
        $q = TipoDescuento::query()
            ->buscar($filters['search'] ?? null)
            ->when(isset($filters['activo']), fn($qq)=>$qq->where('activo',(int)$filters['activo']))
            ->orderBy($filters['sort_by'] ?? 'nombre', $filters['sort_dir'] ?? 'asc');

        return $q->paginate((int)($filters['per_page'] ?? 15));
    }

    public function create(array $data): TipoDescuento
    {
        $data['activo'] = $data['activo'] ?? true;
        return TipoDescuento::create($data);
    }

    public function update(TipoDescuento $td, array $data): TipoDescuento
    {
        $td->fill($data)->save();
        return $td;
    }

    public function toggle(int $id): TipoDescuento
    {
        $td = TipoDescuento::findOrFail($id);
        $td->activo = !$td->activo;
        $td->save();
        return $td;
    }

    public function deactivate(int $id): void
    {
        $td = TipoDescuento::findOrFail($id);
        $td->activo = 0;
        $td->save();
    }

    public function options(bool $soloActivos=true): Collection
    {
        $q = TipoDescuento::query()->orderBy('nombre');
        if ($soloActivos) $q->activos();
        return $q->get(['id','nombre']);
    }
}
