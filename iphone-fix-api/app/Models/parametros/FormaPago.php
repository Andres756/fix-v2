<?php

namespace App\Models\Parametros;

use Illuminate\Database\Eloquent\Model;

class FormaPago extends Model
{
    protected $table = 'formas_pago';
    public $timestamps = false;

    protected $fillable = ['nombre', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Scopes útiles
    public function scopeActivas($q)    { return $q->where('activo', 1); }
    public function scopeBuscar($q,$s)  { return $s ? $q->where('nombre','like',"%{$s}%") : $q; }
}
