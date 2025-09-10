<?php
// app/Models/Parametros/EstadoFactura.php
namespace App\Models\Parametros;
use Illuminate\Database\Eloquent\Model;

class EstadoFactura extends Model
{
    protected $table = 'estados_factura';
    public $timestamps = false;
    protected $fillable = ['nombre','activo'];
    protected $casts = ['activo' => 'boolean'];
    public function scopeBuscar($q,$s){ return $s ? $q->where('nombre','like',"%{$s}%") : $q; }
    public function scopeActivos($q){ return $q->where('activo',1); }
}
