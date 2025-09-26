<?php
// app/Models/Parametros/MotivoIngreso.php

namespace App\Models\Parametros;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventario\EntradaProducto;

class MotivoIngreso extends Model
{
    protected $table = 'motivos_ingreso';
    
    public $timestamps = false;
    
    protected $fillable = [
        'nombre'
    ];

    // RELACIONES
    public function entradas()
    {
        return $this->hasMany(EntradaProducto::class, 'motivo_ingreso_id');
    }
}