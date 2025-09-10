<?php

namespace App\Models\OrdenServicio;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Users\User;

class HistorialEstadoOs extends Model
{
    use HasFactory;

    protected $table = 'historial_estados_os';

    protected $fillable = [
        'orden_id',
        'equipo_os_id',
        'tarea_id',
        'estado_anterior',
        'estado_nuevo',
        'usuario_id',
        'comentario',
        'fecha_cambio'
    ];

    public function ordenServicio()
    {
        return $this->belongsTo(OrdenServicio::class, 'orden_id');
    }

    public function equipo()
    {
        return $this->belongsTo(EquipoOrdenServicio::class, 'equipo_os_id');
    }

    public function tarea()
    {
        return $this->belongsTo(TareaEquipo::class, 'tarea_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
