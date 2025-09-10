<?php

namespace App\Models\Parametros;

use Illuminate\Database\Eloquent\Model;

class EstadoInventario extends Model
{
    protected $table = 'estados_inventario';
    public $timestamps = false;

    protected $fillable = ['nombre', 'mostrar_en_stock'];
    protected $casts = ['mostrar_en_stock' => 'boolean'];

    public function scopeBuscar($q,$s){ return $s ? $q->where('nombre','like',"%{$s}%") : $q; }
    public function scopeVisibles($q){ return $q->where('mostrar_en_stock',1); }
}
