<?php

namespace App\Models\PlanSepare;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoPlanSepare extends Model
{
    use HasFactory;

    protected $table = 'estados_plan_separe';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'codigo',
        'activo'
    ];

    public function planes()
    {
        return $this->hasMany(PlanSepare::class, 'estado_id');
    }
}
