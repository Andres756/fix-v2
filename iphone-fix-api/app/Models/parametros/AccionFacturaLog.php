<?php
// app/Models/Parametros/AccionFacturaLog.php
namespace App\Models\Parametros;
use Illuminate\Database\Eloquent\Model;

class AccionFacturaLog extends Model
{
    protected $table = 'acciones_factura_log';
    public $timestamps = false;
    protected $fillable = ['nombre','activo'];
    protected $casts = ['activo' => 'boolean'];
    public function scopeBuscar($q,$s){ return $s ? $q->where('nombre','like',"%{$s}%") : $q; }
    public function scopeActivas($q){ return $q->where('activo',1); }
}
