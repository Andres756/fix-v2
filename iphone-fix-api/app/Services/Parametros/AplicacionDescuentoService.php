<?php
// app/Services/Parametros/AplicacionDescuentoService.php
namespace App\Services\Parametros;

use App\Models\Parametros\AplicacionDescuento;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AplicacionDescuentoService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $q = AplicacionDescuento::query()
            ->buscar($filters['search'] ?? null)
            ->when(isset($filters['activo']), fn($qq) => $qq->where('activo', (int)$filters['activo']))
            ->orderBy($filters['sort_by'] ?? 'nombre', $filters['sort_dir'] ?? 'asc');

        return $q->paginate((int)($filters['per_page'] ?? 15));
    }

    public function create(array $data): AplicacionDescuento
    {
        $data['activo'] = $data['activo'] ?? true;
        return AplicacionDescuento::create($data);
    }

    public function update(AplicacionDescuento $m, array $data): AplicacionDescuento
    {
        $m->fill($data)->save();
        return $m;
    }

    public function toggle(int $id): AplicacionDescuento
    {
        $m = AplicacionDescuento::findOrFail($id);
        $m->activo = !$m->activo;
        $m->save();
        return $m;
    }

    public function deactivate(int $id): void
    {
        $m = AplicacionDescuento::findOrFail($id);
        $m->activo = 0;
        $m->save();
    }

    public function options(bool $soloActivos = true)
    {
        $q = AplicacionDescuento::query()->orderBy('nombre');
        if ($soloActivos) $q->where('activo', 1);
        return $q->get(['id','nombre']);
    }
}
