<?php

namespace App\Models\Parametros;

use Illuminate\Database\Eloquent\Model;

class ParametroFacturacion extends Model
{
    protected $table = 'parametros_facturacion';
    public $timestamps = false;

    protected $fillable = ['clave','valor','descripcion','activo'];
    protected $casts = ['activo' => 'boolean'];

    public function scopeBuscar($q,$s){
        return $s ? $q->where(function($qq) use ($s){
            $qq->where('clave','like',"%{$s}%")->orWhere('valor','like',"%{$s}%");
        }) : $q;
    }

    public function scopeActivos($q){ return $q->where('activo',1); }
}
