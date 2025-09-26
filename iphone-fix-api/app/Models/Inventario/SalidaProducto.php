<?php
// app/Models/Inventario/SalidaProducto.php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;

class SalidaProducto extends Model
{
    protected $table = 'salidas_producto';
    
    protected $fillable = [
        'inventario_id',
        'tipo_salida',
        'cantidad',
        'costo_unitario',
        'referencia_id',
        'fecha_salida',
        'observaciones'
    ];
    
    protected $casts = [
        'cantidad'       => 'int',
        'costo_unitario' => 'decimal:2',
        'fecha_salida'   => 'datetime'
    ];

    // Tipos de salida permitidos
    const TIPO_VENTA           = 'venta';
    const TIPO_ORDEN_SERVICIO  = 'orden_servicio';
    const TIPO_AJUSTE          = 'ajuste';
    const TIPO_PERDIDA         = 'perdida';

    // RELACIONES
    public function inventario() 
    { 
        return $this->belongsTo(Inventario::class, 'inventario_id'); 
    }

    // SCOPES
    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo_salida', $tipo);
    }

    public function scopeVentas($query)
    {
        return $query->where('tipo_salida', self::TIPO_VENTA);
    }

    public function scopeOrdenesServicio($query)
    {
        return $query->where('tipo_salida', self::TIPO_ORDEN_SERVICIO);
    }
}