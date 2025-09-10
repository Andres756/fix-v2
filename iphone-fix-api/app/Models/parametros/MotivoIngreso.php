<?php
// app/Models/Parametros/MotivoIngreso.php
namespace App\Models\Parametros;
use Illuminate\Database\Eloquent\Model;

class MotivoIngreso extends Model
{
    protected $table = 'motivos_ingreso';
    public $timestamps = false;
    protected $fillable = ['nombre'];
    public function scopeBuscar($q,$s){ return $s ? $q->where('nombre','like',"%{$s}%") : $q; }
}
