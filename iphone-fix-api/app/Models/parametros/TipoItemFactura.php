<?php
// app/Models/Parametros/TipoItemFactura.php
namespace App\Models\Parametros;
use Illuminate\Database\Eloquent\Model;

class TipoItemFactura extends Model
{
    protected $table = 'tipos_item_factura';
    public $timestamps = false;
    protected $fillable = ['nombre','activo'];
    protected $casts = ['activo' => 'boolean'];
    public function scopeBuscar($q,$s){ return $s ? $q->where('nombre','like',"%{$s}%") : $q; }
    public function scopeActivos($q){ return $q->where('activo',1); }
}
