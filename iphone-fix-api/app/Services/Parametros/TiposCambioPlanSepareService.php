<?php
// app/Services/Parametros/TiposCambioPlanSepareService.php
namespace App\Services\Parametros;

use App\Models\Parametros\TipoCambioPlanSepare;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TiposCambioPlanSepareService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $q = TipoCambioPlanSepare::query()
            ->buscar($filters['search'] ?? null)
            ->when(isset($filters['activo']), fn($qq) => $qq->where('activo', (int)$filters['activo']))
            ->orderBy($filters['sort_by'] ?? 'nombre', $filters['sort_dir'] ?? 'asc');

        return $q->paginate((int)($filters['per_page'] ?? 15));
    }

    public function create(array $data): TipoCambioPlanSepare
    {
        $data['activo'] = $data['activo'] ?? true;
        return TipoCambioPlanSepare::create($data);
    }

    public function update(TipoCambioPlanSepare $m, array $data): TipoCambioPlanSepare
    {
        $m->fill($data)->save();
        return $m;
    }

    public function toggle(int $id): TipoCambioPlanSepare
    {
        $m = TipoCambioPlanSepare::findOrFail($id);
        $m->activo = !$m->activo;
        $m->save();
        return $m;
    }

    public function deactivate(int $id): void
    {
        $m = TipoCambioPlanSepare::findOrFail($id);
        $m->activo = 0;
        $m->save();
    }

    public function options(bool $soloActivos = true): Collection
    {
        $q = TipoCambioPlanSepare::query()->orderBy('nombre');
        if ($soloActivos) $q->where('activo', 1);
        return $q->get(['id','nombre']);
    }
}
