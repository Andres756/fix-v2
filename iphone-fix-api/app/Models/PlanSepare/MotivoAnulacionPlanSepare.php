<?php

namespace App\Models\PlanSepare;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotivoAnulacionPlanSepare extends Model
{
    use HasFactory;

    protected $table = 'motivos_anulacion_plansepare';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo'
    ];
}
