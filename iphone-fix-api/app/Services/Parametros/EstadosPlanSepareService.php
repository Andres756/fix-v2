<?php
// app/Services/Parametros/EstadosPlanSepareService.php
namespace App\Services\Parametros;

use App\Models\Parametros\EstadoPlanSepare;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EstadosPlanSepareService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $q = EstadoPlanSepare::query()
            ->buscar($filters['search'] ?? null)
            ->when(isset($filters['activo']), fn($qq) => $qq->where('activo', (int)$filters['activo']))
            ->orderBy($filters['sort_by'] ?? 'nombre', $filters['sort_dir'] ?? 'asc');

        return $q->paginate((int)($filters['per_page'] ?? 15));
    }

    public function create(array $data): EstadoPlanSepare
    {
        $data['activo'] = $data['activo'] ?? true;
        return EstadoPlanSepare::create($data);
    }

    public function update(EstadoPlanSepare $m, array $data): EstadoPlanSepare
    {
        $m->fill($data)->save();
        return $m;
    }

    public function toggle(int $id): EstadoPlanSepare
    {
        $m = EstadoPlanSepare::findOrFail($id);
        $m->activo = !$m->activo;
        $m->save();
        return $m;
    }

    public function deactivate(int $id): void
    {
        $m = EstadoPlanSepare::findOrFail($id);
        $m->activo = 0;
        $m->save();
    }

    public function options(bool $soloActivos = true): Collection
    {
        $q = EstadoPlanSepare::query()->orderBy('nombre');
        if ($soloActivos) $q->where('activo', 1);
        return $q->get(['id','nombre']);
    }
}
