<?php
// app/Models/PlanSepare/PlanSepareLog.php
namespace App\Models\PlanSepare;

use Illuminate\Database\Eloquent\Model;
use App\Models\Parametros\TipoCambioPlanSepare;
use App\Models\Inventario\Inventario;
use App\Models\User;

class PlanSepareLog extends Model
{
    protected $table = 'plan_separe_log';
    public $timestamps = false;
    protected $fillable = [
        'plan_id','tipo_cambio_id','inventario_anterior_id','inventario_nuevo_id',
        'precio_anterior','precio_nuevo','monto_devuelto','observaciones','usuario_id','fecha'
    ];
    protected $casts = [
        'precio_anterior'=>'decimal:2','precio_nuevo'=>'decimal:2',
        'monto_devuelto'=>'decimal:2','fecha'=>'datetime'
    ];

    public function plan(){ return $this->belongsTo(PlanSepare::class, 'plan_id'); }
    public function tipoCambio(){ return $this->belongsTo(TipoCambioPlanSepare::class, 'tipo_cambio_id'); }
    public function inventarioAnterior(){ return $this->belongsTo(Inventario::class, 'inventario_anterior_id'); }
    public function inventarioNuevo(){ return $this->belongsTo(Inventario::class, 'inventario_nuevo_id'); }
    public function usuario(){ return $this->belongsTo(User::class, 'usuario_id'); }
}
