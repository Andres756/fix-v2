<?php

namespace App\Models\OrdenServicio;

use App\Models\Inventario\Proveedor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RepuestoOsExterno extends Model
{
    use HasFactory;

    protected $table = 'repuestos_os_externos';

    protected $fillable = [
        'equipo_os_id',
        'descripcion',
        'cantidad',
        'costo_unitario',
        'proveedor_id',
        'observaciones',
        'fecha_gasto'
    ];

    // costo_total se calcula automáticamente, no va en fillable

    protected $casts = [
        'fecha_gasto' => 'date',
        'costo_unitario' => 'decimal:2',
        'costo_total' => 'decimal:2',
        'cantidad' => 'integer',
    ];

    // Accessor para calcular costo_total automáticamente
    public function getCostoTotalAttribute()
    {
        return round($this->cantidad * $this->costo_unitario, 2);
    }

    // Boot method para calcular y guardar costo_total en la BD
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($repuesto) {
            $repuesto->costo_total = round($repuesto->cantidad * $repuesto->costo_unitario, 2);
        });
        
        static::updating(function ($repuesto) {
            if ($repuesto->isDirty(['cantidad', 'costo_unitario'])) {
                $repuesto->costo_total = round($repuesto->cantidad * $repuesto->costo_unitario, 2);
            }
        });
    }

    // Relaciones
    public function equipo()
    {
        return $this->belongsTo(EquipoOrdenServicio::class, 'equipo_os_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }
}