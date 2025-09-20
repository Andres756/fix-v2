<?php

namespace App\Models\OrdenServicio;

use Illuminate\Database\Eloquent\Model;

class TareaEquipoHistorial extends Model
{
    protected $table = 'tarea_equipo_historial';

    protected $fillable = [
        'tarea_equipo_id',
        'tecnico_id',
        'estado_anterior',
        'estado_nuevo',
        'cambiado_en',
    ];

    public function tarea()
    {
        return $this->belongsTo(TareaEquipo::class, 'tarea_equipo_id');
    }

    public function tecnico()
    {
        return $this->belongsTo(\App\Models\User::class, 'tecnico_id');
    }
}
