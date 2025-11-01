<?php

namespace App\Models\OrdenServicio;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class EquipoOrdenServicio extends Model
{
    use HasFactory;

    protected $table = 'equipos_orden_servicio';

    protected $fillable = [
        'orden_id',
        'imei_serial',
        'marca',
        'modelo',
        'descripcion_problema',
        'contrasena_equipo',
        'valor_estimado',
        'fecha_estimada_entrega',
        'tecnico_asignado',
        'comision_habilitada',
        'tipo_comision',
        'valor_comision',
        'estado',
        'observaciones',
        'fecha_finalizacion',        // pendiente | en_proceso | finalizado | cancelado
        'facturado',       // tinyint
        'entregado',       // tinyint
        'factura_id'      // nullable
    ];

    protected $casts = [
        'valor_estimado'          => 'decimal:2',
        'valor_comision'          => 'decimal:2',
        'comision_habilitada'     => 'boolean',
        'fecha_estimada_entrega'  => 'date',
        'fecha_finalizacion'      => 'datetime',
        'facturado'               => 'boolean',
        'entregado'               => 'boolean',
    ];

    // Relaciones
    public function orden()
    {
        return $this->belongsTo(OrdenServicio::class, 'orden_id');
    }

    public function tecnico()
    {
        return $this->belongsTo(User::class, 'tecnico_asignado');
    }

        public function tareas()
    {
        // clave foránea en tareas_equipo = equipo_os_id
        return $this->hasMany(TareaEquipo::class, 'equipo_os_id');
    }

        // Relación con repuestos desde inventario
    public function repuestosInventario()
    {
        return $this->hasMany(
            \App\Models\OrdenServicio\RepuestoOsInventario::class,
            'equipo_os_id'
        );
    }

        // Relación con repuestos externos desde inventario
        public function repuestosExternos()
    {
        return $this->hasMany(
            \App\Models\OrdenServicio\RepuestoOsExterno::class,
            'equipo_os_id'
        );
    }
}