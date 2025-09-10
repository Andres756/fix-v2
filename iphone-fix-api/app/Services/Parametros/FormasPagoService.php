<?php

namespace App\Services\Parametros;

use App\Models\Parametros\FormaPago;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class FormasPagoService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $search   = $filters['search']   ?? null;
        $activo   = $filters['activo']   ?? null;
        $perPage  = (int)($filters['per_page'] ?? 15);
        $sortBy   = $filters['sort_by']  ?? 'nombre';
        $sortDir  = $filters['sort_dir'] ?? 'asc';

        $q = FormaPago::query()
            ->buscar($search)
            ->when(!is_null($activo), fn($qq) => $qq->where('activo', (int) $activo))
            ->orderBy($sortBy, $sortDir);

        return $q->paginate($perPage);
    }

    public function create(array $data): FormaPago
    {
        $data['activo'] = $data['activo'] ?? true;
        return FormaPago::create($data);
    }

    public function update(FormaPago $fp, array $data): FormaPago
    {
        $fp->fill($data)->save();
        return $fp;
    }

    public function toggle(int $id): FormaPago
    {
        $fp = FormaPago::findOrFail($id);
        $fp->activo = !$fp->activo;
        $fp->save();
        return $fp;
    }

    // Para combos
    public function options(?bool $soloActivas = true): Collection
    {
        $q = FormaPago::query()->orderBy('nombre');
        if ($soloActivas) $q->activas();
        return $q->get(['id','nombre']);
    }

    // “Eliminar” lógico
    public function deactivate(int $id): void
    {
        $fp = FormaPago::findOrFail($id);
        $fp->activo = 0;
        $fp->save();
    }
}
