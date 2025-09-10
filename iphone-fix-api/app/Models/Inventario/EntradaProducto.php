<?php
// app/Models/Inventario/EntradaProducto.php
namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use App\Models\Parametros\MotivoIngreso;

class EntradaProducto extends Model
{
    protected $table = 'entradas_producto';
    public $timestamps = false;
    protected $fillable = ['inventario_id','lote_id','motivo_ingreso_id','cantidad','fecha_entrada','notas'];
    protected $casts = ['cantidad'=>'int','fecha_entrada'=>'date'];
    public function inventario(){ return $this->belongsTo(Inventario::class, 'inventario_id'); }
    public function lote(){ return $this->belongsTo(Lote::class, 'lote_id'); }
    public function motivo(){ return $this->belongsTo(MotivoIngreso::class, 'motivo_ingreso_id'); }
}
