<?php
// app/Models/Inventario/DetalleEquipo.php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;

class DetalleEquipo extends Model
{
    protected $table = 'detalles_equipos';
    public $timestamps = false;
    protected $primaryKey = 'inventario_id';
    public $incrementing = false;
    
    protected $fillable = [
        'inventario_id',
        'modelo_equipo_id',  // ✅ NUEVO
        'imei_1',
        'imei_2',
        'estado_fisico',
        'version_ios',
        'almacenamiento',
        'color'
    ];

    protected $casts = [
        'modelo_equipo_id' => 'integer',
    ];

    // ===========================
    // RELACIONES
    // ===========================
    
    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'inventario_id');
    }

    /**
     * ✅ NUEVA: Relación con modelo de equipo
     */
    public function modeloEquipo()
    {
        return $this->belongsTo(ModeloEquipo::class, 'modelo_equipo_id');
    }
}