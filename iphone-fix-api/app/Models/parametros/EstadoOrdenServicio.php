<?php
// app/Models/Parametros/EstadoOrdenServicio.php
namespace App\Models\Parametros;
use Illuminate\Database\Eloquent\Model;

class EstadoOrdenServicio extends Model
{
    protected $table = 'estados_orden_servicio';
    public $timestamps = false;
    protected $fillable = ['nombre'];
    public function scopeBuscar($q,$s){ return $s ? $q->where('nombre','like',"%{$s}%") : $q; }
}
