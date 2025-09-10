<?php
// app/Models/Parametros/TipoCambioPlanSepare.php
namespace App\Models\Parametros;
use Illuminate\Database\Eloquent\Model;

class TipoCambioPlanSepare extends Model
{
    protected $table = 'tipos_cambio_plan_separe';
    public $timestamps = false;
    protected $fillable = ['nombre','activo'];
    protected $casts = ['activo' => 'boolean'];
    public function scopeBuscar($q,$s){ return $s ? $q->where('nombre','like',"%{$s}%") : $q; }
    public function scopeActivos($q){ return $q->where('activo',1); }
}
