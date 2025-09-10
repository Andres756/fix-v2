<?php
// app/Services/Parametros/MotivosIngresoService.php
namespace App\Services\Parametros;

use App\Models\Parametros\MotivoIngreso;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MotivosIngresoService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $q = MotivoIngreso::query()
            ->buscar($filters['search'] ?? null)
            ->orderBy($filters['sort_by'] ?? 'nombre', $filters['sort_dir'] ?? 'asc');

        return $q->paginate((int)($filters['per_page'] ?? 15));
    }

    public function create(array $data): MotivoIngreso
    {
        return MotivoIngreso::create($data);
    }

    public function update(MotivoIngreso $m, array $data): MotivoIngreso
    {
        $m->fill($data)->save();
        return $m;
    }

    public function options()
    {
        return MotivoIngreso::query()
            ->orderBy('nombre')
            ->get(['id','nombre']);
    }
}
