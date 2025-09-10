<?php

namespace App\Services\Parametros;

use App\Models\Parametros\EstadoInventario;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EstadosInventarioService
{
    public function list(array $filters=[]): LengthAwarePaginator
    {
        $q = EstadoInventario::query()
            ->buscar($filters['search'] ?? null)
            ->when(isset($filters['mostrar_en_stock']), fn($qq)=>$qq->where('mostrar_en_stock',(int)$filters['mostrar_en_stock']))
            ->orderBy($filters['sort_by'] ?? 'nombre', $filters['sort_dir'] ?? 'asc');

        return $q->paginate((int)($filters['per_page'] ?? 15));
    }

    public function create(array $data): EstadoInventario
    {
        $data['mostrar_en_stock'] = $data['mostrar_en_stock'] ?? true;
        return EstadoInventario::create($data);
    }

    public function update(EstadoInventario $e, array $data): EstadoInventario
    {
        $e->fill($data)->save();
        return $e;
    }

    public function toggleVisible(int $id): EstadoInventario
    {
        $e = EstadoInventario::findOrFail($id);
        $e->mostrar_en_stock = !$e->mostrar_en_stock;
        $e->save();
        return $e;
    }

    public function options(bool $soloVisibles=true): Collection
    {
        $q = EstadoInventario::query()->orderBy('nombre');
        if ($soloVisibles) $q->visibles();
        return $q->get(['id','nombre']);
    }
}
