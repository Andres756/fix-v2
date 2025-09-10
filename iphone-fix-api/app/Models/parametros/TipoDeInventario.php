<?php
// app/Models/Parametros/TipoDeInventario.php
namespace App\Models\Parametros;
use Illuminate\Database\Eloquent\Model;

class TipoDeInventario extends Model
{
    protected $table = 'tipos_de_inventario';
    public $timestamps = false;
    protected $fillable = ['nombre'];
    public function scopeBuscar($q,$s){ return $s ? $q->where('nombre','like',"%{$s}%") : $q; }
}
