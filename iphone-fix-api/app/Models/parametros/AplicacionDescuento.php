<?php
// app/Models/Parametros/AplicacionDescuento.php
namespace App\Models\Parametros;
use Illuminate\Database\Eloquent\Model;

class AplicacionDescuento extends Model
{
    protected $table = 'aplicacion_descuento';
    public $timestamps = false;
    protected $fillable = ['nombre','activo'];
    protected $casts = ['activo' => 'boolean'];
    public function scopeBuscar($q,$s){ return $s ? $q->where('nombre','like',"%{$s}%") : $q; }
    public function scopeActivas($q){ return $q->where('activo',1); }
}
