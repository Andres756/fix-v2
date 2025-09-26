<?php

namespace App\Models\OrdenServicio;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Parametros\TipoTrabajo; // 👈 TipoTrabajo sigue en Parametros

class TareaEquipo extends Model
{
    use HasFactory;

    protected $table = 'tareas_equipo';

    protected $fillable = [
        'equipo_os_id',
        'tipo_trabajo_id',
        'costo_aplicado',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'costo_aplicado' => 'decimal:2',
    ];

    public function equipo()
    {
        return $this->belongsTo(EquipoOrdenServicio::class, 'equipo_os_id');
    }

    public function tipoTrabajo()
    {
        return $this->belongsTo(TipoTrabajo::class, 'tipo_trabajo_id');
    }

    // POR ESTA:
    public function historial()
    {
        return $this->hasMany(TareaEquipoHistorial::class, 'tarea_equipo_id'); // ✅
    }
}
