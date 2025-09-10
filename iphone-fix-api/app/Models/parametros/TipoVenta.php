<?php

namespace App\Models\Parametros;

use Illuminate\Database\Eloquent\Model;

class TipoVenta extends Model
{
    protected $table = 'tipos_venta';
    public $timestamps = false;

    protected $fillable = ['nombre', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // nombre es VARCHAR(20) en BD → validaremos eso en Requests
    public function scopeActivos($q)   { return $q->where('activo', 1); }
    public function scopeBuscar($q,$s) { return $s ? $q->where('nombre','like',"%{$s}%") : $q; }
}
