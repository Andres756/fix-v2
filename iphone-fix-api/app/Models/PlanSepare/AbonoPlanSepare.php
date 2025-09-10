<?php
// app/Models/PlanSepare/AbonoPlanSepare.php
namespace App\Models\PlanSepare;

use Illuminate\Database\Eloquent\Model;
use App\Models\Parametros\FormaPago;
use App\Models\User;

class AbonoPlanSepare extends Model
{
    protected $table = 'abonos_plan_separe';
    public $timestamps = false;
    protected $fillable = ['plan_id','monto','forma_pago_id','usuario_id','fecha_abono'];
    protected $casts = ['monto'=>'decimal:2','fecha_abono'=>'date'];

    public function plan(){ return $this->belongsTo(PlanSepare::class, 'plan_id'); }
    public function formaPago(){ return $this->belongsTo(FormaPago::class, 'forma_pago_id'); }
    public function usuario(){ return $this->belongsTo(User::class, 'usuario_id'); }
}
