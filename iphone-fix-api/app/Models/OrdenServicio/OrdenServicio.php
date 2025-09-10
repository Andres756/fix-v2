<?php

namespace App\Models\OrdenServicio;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Cliente; // Relación con el modelo Cliente
use App\Models\User; // Relación con el modelo User (Técnico)

class OrdenServicio extends Model
{
    use HasFactory;

    // Definir la tabla
    protected $table = 'ordenes_servicio';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'codigo_orden', 
        'cliente_id', 
        'tecnico_asignado_os', 
        'estado', 
        'comision_habilitada', 
        'observaciones_generales', 
        'fecha_creacion', 
        'fecha_cierre'
    ];

    // Relación con el Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    // Relación con los equipos asociados a la OS
    public function equipos()
    {
        return $this->hasMany(EquipoOrdenServicio::class, 'orden_id');
    }
}
