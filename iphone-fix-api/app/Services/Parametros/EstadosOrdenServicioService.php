<?php
// app/Services/Parametros/EstadosOrdenServicioService.php
namespace App\Services\Parametros;

use App\Models\Parametros\EstadoOrdenServicio;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EstadosOrdenServicioService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $q = EstadoOrdenServicio::query()
            ->buscar($filters['search'] ?? null)
            ->orderBy($filters['sort_by'] ?? 'nombre', $filters['sort_dir'] ?? 'asc');

        return $q->paginate((int)($filters['per_page'] ?? 15));
    }

    public function create(array $data): EstadoOrdenServicio
    {
        return EstadoOrdenServicio::create($data);
    }

    public function update(EstadoOrdenServicio $m, array $data): EstadoOrdenServicio
    {
        $m->fill($data)->save();
        return $m;
    }

    public function options()
    {
        return EstadoOrdenServicio::query()
            ->orderBy('nombre')
            ->get(['id','nombre']);
    }
}
