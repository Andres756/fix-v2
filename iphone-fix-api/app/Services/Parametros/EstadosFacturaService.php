<?php
// app/Services/Parametros/EstadosFacturaService.php
namespace App\Services\Parametros;

use App\Models\Parametros\EstadoFactura;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EstadosFacturaService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $q = EstadoFactura::query()
            ->buscar($filters['search'] ?? null)
            ->when(isset($filters['activo']), fn($qq) => $qq->where('activo', (int)$filters['activo']))
            ->orderBy($filters['sort_by'] ?? 'nombre', $filters['sort_dir'] ?? 'asc');

        return $q->paginate((int)($filters['per_page'] ?? 15));
    }

    public function create(array $data): EstadoFactura
    {
        $data['activo'] = $data['activo'] ?? true;
        return EstadoFactura::create($data);
    }

    public function update(EstadoFactura $m, array $data): EstadoFactura
    {
        $m->fill($data)->save();
        return $m;
    }

    public function toggle(int $id): EstadoFactura
    {
        $m = EstadoFactura::findOrFail($id);
        $m->activo = !$m->activo;
        $m->save();
        return $m;
    }

    public function deactivate(int $id): void
    {
        $m = EstadoFactura::findOrFail($id);
        $m->activo = 0;
        $m->save();
    }

    public function options(bool $soloActivos = true)
    {
        $q = EstadoFactura::query()->orderBy('nombre');
        if ($soloActivos) $q->where('activo', 1);
        return $q->get(['id','nombre']);
    }
}
