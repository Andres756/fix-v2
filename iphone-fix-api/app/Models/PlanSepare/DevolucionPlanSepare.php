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
        'monto_total',
        'monto_devuelto',
        'porcentaje_devolucion',
        'forma_pago_id',
        'usuario_id',
        'observaciones',
        'created_at',
    ];

    public $timestamps = false; // usamos created_at manualmente

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
