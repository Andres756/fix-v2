<?php
// app/Models/PlanSepare/PlanSepare.php
namespace App\Models\PlanSepare;

use Illuminate\Database\Eloquent\Model;
use App\Models\Clientes\Cliente;
use App\Models\Inventario\Inventario;
use App\Models\Parametros\EstadoPlanSepare;

class PlanSepare extends Model
{
    protected $table = 'plan_separe';
    protected $fillable = [
        'cliente_id','inventario_id','inventario_id_asignado','cambio_equipo',
        'precio_total','porcentaje_minimo','abono_inicial','monto_devuelto',
        'estado_id','observaciones'
    ];
    protected $casts = [
        'cambio_equipo'=>'bool','precio_total'=>'decimal:2',
        'porcentaje_minimo'=>'decimal:2','abono_inicial'=>'decimal:2',
        'monto_devuelto'=>'decimal:2'
    ];

    public function cliente(){ return $this->belongsTo(Cliente::class, 'cliente_id'); }
    public function inventario(){ return $this->belongsTo(Inventario::class, 'inventario_id'); }
    public function inventarioAsignado(){ return $this->belongsTo(Inventario::class, 'inventario_id_asignado'); }
    public function estado(){ return $this->belongsTo(EstadoPlanSepare::class, 'estado_id'); }

    public function abonos(){ return $this->hasMany(AbonoPlanSepare::class, 'plan_id'); }
    public function logs(){ return $this->hasMany(PlanSepareLog::class, 'plan_id'); }
}
