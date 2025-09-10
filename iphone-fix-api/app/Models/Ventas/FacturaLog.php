<?php
// app/Models/Ventas/FacturaLog.php
namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Parametros\AccionFacturaLog;

class FacturaLog extends Model
{
    protected $table = 'facturas_log';
    public $timestamps = false;
    protected $fillable = ['factura_id','accion_id','usuario_id','detalle','created_at'];
    protected $casts = ['created_at'=>'datetime'];

    public function factura(){ return $this->belongsTo(Factura::class, 'factura_id'); }
    public function accion(){ return $this->belongsTo(AccionFacturaLog::class, 'accion_id'); }
    public function usuario(){ return $this->belongsTo(User::class, 'usuario_id'); }
}
