<?php
// app/Models/Inventario/MotivoIngreso.php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;

class MotivoIngreso extends Model
{
    protected $table = 'motivos_ingreso';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'activo'
    ];
}