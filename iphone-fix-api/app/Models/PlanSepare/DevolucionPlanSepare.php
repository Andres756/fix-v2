<?php

namespace App\Models\PlanSepare;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevolucionPlanSepare extends Model
{
    use HasFactory;

    protected $table = 'devoluciones_plan_separe';

    protected $fillable = [
        'plan_separe_id',
        'valor_devolucion',
        'porcentaje_devolucion',
        'motivo',
        'usuario_id',
        'fecha_devolucion',
        'observaciones',
    ];

    public $timestamps = false; // usamos fecha_devolucion manualmente

    // 🔹 Relación con el plan
    public function plan()
    {
        return $this->belongsTo(PlanSepare::class, 'plan_separe_id');
    }

    // 🔹 Relación con el usuario que realizó la devolución
    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }
}
