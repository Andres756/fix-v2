<?php
// app/Services/Parametros/AccionesFacturaLogService.php
namespace App\Services\Parametros;

use App\Models\Parametros\AccionFacturaLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AccionesFacturaLogService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $q = AccionFacturaLog::query()
            ->buscar($filters['search'] ?? null)
            ->when(isset($filters['activo']), fn($qq) => $qq->where('activo', (int)$filters['activo']))
            ->orderBy($filters['sort_by'] ?? 'nombre', $filters['sort_dir'] ?? 'asc');

        return $q->paginate((int)($filters['per_page'] ?? 15));
    }

    public function create(array $data): AccionFacturaLog
    {
        $data['activo'] = $data['activo'] ?? true;
        return AccionFacturaLog::create($data);
    }

    public function update(AccionFacturaLog $m, array $data): AccionFacturaLog
    {
        $m->fill($data)->save();
        return $m;
    }

    public function toggle(int $id): AccionFacturaLog
    {
        $m = AccionFacturaLog::findOrFail($id);
        $m->activo = !$m->activo;
        $m->save();
        return $m;
    }

    public function deactivate(int $id): void
    {
        $m = AccionFacturaLog::findOrFail($id);
        $m->activo = 0;
        $m->save();
    }

    public function options(bool $soloActivos = true): Collection
    {
        $q = AccionFacturaLog::query()->orderBy('nombre');
        if ($soloActivos) $q->where('activo', 1);
        return $q->get(['id','nombre']);
    }
}
