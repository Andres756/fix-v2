<?php
// app/Services/Parametros/TiposTrabajoService.php
namespace App\Services\Parametros;

use App\Models\Parametros\TipoTrabajo;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TiposTrabajoService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $q = TipoTrabajo::query()
            ->buscar($filters['search'] ?? null)
            ->when(!empty($filters['tipo_pago_tecnico']), fn($qq) => $qq->where('tipo_pago_tecnico', $filters['tipo_pago_tecnico']))
            ->when(isset($filters['costo_min']), fn($qq) => $qq->where('costo_cliente', '>=', (float)$filters['costo_min']))
            ->when(isset($filters['costo_max']), fn($qq) => $qq->where('costo_cliente', '<=', (float)$filters['costo_max']))
            ->orderBy($filters['sort_by'] ?? 'nombre', $filters['sort_dir'] ?? 'asc');

        return $q->paginate((int)($filters['per_page'] ?? 15));
    }

    public function create(array $data): TipoTrabajo
    {
        return TipoTrabajo::create($data);
    }

    public function update(TipoTrabajo $m, array $data): TipoTrabajo
    {
        $m->fill($data)->save();
        return $m;
    }

    public function options()
    {
        return TipoTrabajo::query()->orderBy('nombre')->get(['id','nombre']);
    }
}
