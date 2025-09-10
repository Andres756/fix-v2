<?php

namespace App\Models\Parametros;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\OrdenServicio\TareaEquipo;

class TipoTrabajo extends Model
{
    use HasFactory;

    // Si tu tabla se llama 'tipos_trabajo', perfecto.
    protected $table = 'tipos_trabajo';

    // Si tu tabla NO tiene created_at/updated_at, descomenta:
    // public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'costo_sugerido',       // costo sugerido que se aplicará por defecto a la tarea
        'tipo_pago_tecnico',   // 'porcentaje' | 'fijo' (ajusta según tu enum)
        'valor_pago_tecnico',
    ];

    protected $casts = [
        'costo_sugerido'      => 'decimal:2',
        'valor_pago_tecnico' => 'decimal:2',
    ];

    /** Búsqueda simple por nombre */
    public function scopeBuscar($q, $s)
    {
        return $s ? $q->where('nombre', 'like', "%{$s}%") : $q;
    }

    /** Relación: un tipo de trabajo puede estar en muchas tareas */
    public function tareas()
    {
        return $this->hasMany(TareaEquipo::class, 'tipo_trabajo_id');
    }

    /**
     * Alias opcional para el front:
     * permite leer 'costo' y obtener 'costo_sugerido'
     */
    protected $appends = ['costo'];

    public function getCostoAttribute()
    {
        return $this->costo_sugerido;
    }
}
