<?php

namespace App\Models\PlanSepare;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class LogPlanSepare extends Model
{
    use HasFactory;

    protected $table = 'plan_separe_log';
    public $timestamps = false;

    protected $fillable = [
        'plan_separe_id',
        'accion',
        'descripcion',
        'usuario_id'
    ];

    public function plan()
    {
        return $this->belongsTo(PlanSepare::class, 'plan_separe_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
