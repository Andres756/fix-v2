<?php

namespace App\Services\Parametros;

use App\Models\Parametros\ParametroFacturacion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ParametrosFacturacionService
{
    public function list(array $filters=[]): LengthAwarePaginator
    {
        $q = ParametroFacturacion::query()
            ->buscar($filters['search'] ?? null)
            ->when(isset($filters['activo']), fn($qq)=>$qq->where('activo',(int)$filters['activo']))
            ->orderBy($filters['sort_by'] ?? 'clave', $filters['sort_dir'] ?? 'asc');

        return $q->paginate((int)($filters['per_page'] ?? 15));
    }

    public function create(array $data): ParametroFacturacion
    {
        $data['activo'] = $data['activo'] ?? true;
        return ParametroFacturacion::create($data);
    }

    public function update(ParametroFacturacion $p, array $data): ParametroFacturacion
    {
        $p->fill($data)->save();
        return $p;
    }

    public function getByClave(string $clave): ?ParametroFacturacion
    {
        return ParametroFacturacion::where('clave', mb_strtoupper(trim($clave)))->first();
    }
}
