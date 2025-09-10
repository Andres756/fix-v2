<?php

namespace App\Models\Parametros;

use Illuminate\Database\Eloquent\Model;

class TipoDescuento extends Model
{
    protected $table = 'tipos_descuento';
    public $timestamps = false;

    protected $fillable = ['nombre', 'activo'];
    protected $casts = ['activo' => 'boolean'];

    public function scopeActivos($q){ return $q->where('activo', 1); }
    public function scopeBuscar($q,$s){ return $s ? $q->where('nombre','like',"%{$s}%") : $q; }
}
