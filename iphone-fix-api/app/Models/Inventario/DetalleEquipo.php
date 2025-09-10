<?php
// app/Models/Inventario/DetalleEquipo.php
namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;

class DetalleEquipo extends Model
{
    protected $table = 'detalles_equipos';
    public $timestamps = false;
    protected $primaryKey = 'inventario_id';
    public $incrementing = false;
    protected $fillable = ['inventario_id','imei_1','imei_2','estado_fisico','version_ios','almacenamiento','color'];
    public function inventario(){ return $this->belongsTo(Inventario::class, 'inventario_id'); }
}
