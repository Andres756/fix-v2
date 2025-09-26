<?php
// app/Models/Inventario/Lote.php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lote extends Model
{
    use HasFactory;
    
    protected $table = 'lotes';
    
    protected $fillable = [
        'numero_lote',      // Renombrado desde codigo_lote
        'proveedor_id',
        'costo_flete',      // Renombrado desde costo_envio
        'fecha_ingreso',
        'notas'
    ];
    
    protected $casts = [
        'costo_flete'   => 'decimal:2',
        'fecha_ingreso' => 'date'
    ];

    // RELACIONES
    public function proveedor() 
    { 
        return $this->belongsTo(Proveedor::class, 'proveedor_id'); 
    }
    
    public function entradas() 
    { 
        return $this->hasMany(EntradaProducto::class, 'lote_id'); 
    }
}