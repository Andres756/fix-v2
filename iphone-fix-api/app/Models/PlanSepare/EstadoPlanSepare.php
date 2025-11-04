<?php

namespace App\Models\PlanSepare;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoPlanSepare extends Model
{
    use HasFactory;

    protected $table = 'estados_plan_separe';

    protected $fillable = [
        'nombre',
        'codigo',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * 🔁 Relación inversa: un estado puede tener muchos planes.
     */
    public function planes()
    {
        return $this->hasMany(PlanSepare::class, 'estado_id');
    }

    /**
     * 📌 Scope: obtener solo estados activos.
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }

    /**
     * 📌 Scope: buscar por código (ejemplo: EstadoPlanSepare::codigo('RES'))
     */
    public function scopeCodigo($query, string $codigo)
    {
        return $query->where('codigo', strtoupper($codigo));
    }
}
