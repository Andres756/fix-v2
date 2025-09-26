<?php
// app/Models/Inventario/EntradaProducto.php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use App\Models\Parametros\MotivoIngreso;

class EntradaProducto extends Model
{
    protected $table = 'entradas_producto';
    
    protected $fillable = [
        'inventario_id',
        'lote_id',
        'motivo_ingreso_id',
        'cantidad',
        'costo_unitario',
        'fecha_entrada',
        'observaciones'
    ];
    
    protected $casts = [
        'cantidad'       => 'int',
        'costo_unitario' => 'decimal:2',
        'fecha_entrada'  => 'datetime'
    ];

    // RELACIONES
    public function inventario() 
    { 
        return $this->belongsTo(Inventario::class, 'inventario_id'); 
    }
    
    public function lote() 
    { 
        return $this->belongsTo(Lote::class, 'lote_id'); 
    }
    
    public function motivo() 
    { 
        return $this->belongsTo(MotivoIngreso::class, 'motivo_ingreso_id'); 
    }
}