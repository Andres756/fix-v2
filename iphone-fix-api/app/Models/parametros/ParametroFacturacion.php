<?php

namespace App\Models\Parametros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParametroFacturacion extends Model
{
    use HasFactory;

    protected $table = 'parametros_facturacion';
    public $timestamps = false;

    protected $fillable = [
        'prefijo',
        'consecutivo_actual',
        'sede_id',
        'descripcion',
    ];
}
