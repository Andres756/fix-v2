<?php

namespace App\Models\PlanSepare;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;
use App\Models\Inventario\Inventario;
use App\Models\User;

class PlanSepare extends Model
{
    use HasFactory;

    protected $table = 'plan_separe';

    protected $fillable = [
        'cliente_id',
        'inventario_id',
        'inventario_id_asignado',
        'cambio_equipo',
        'precio_total',
        'porcentaje_minimo',
        'abono_inicial',
        'monto_devuelto',
        'estado_id',
        'usuario_id',
        'observaciones'
    ];

    // Relaciones principales
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'inventario_id');
    }

    public function inventarioAsignado()
    {
        return $this->belongsTo(Inventario::class, 'inventario_id_asignado');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoPlanSepare::class, 'estado_id');
    }

    public function abonos()
    {
        return $this->hasMany(AbonoPlanSepare::class, 'plan_separe_id');
    }

    public function logs()
    {
        return $this->hasMany(LogPlanSepare::class, 'plan_separe_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Método auxiliar para obtener total abonado
    public function getTotalAbonosAttribute()
    {
        return $this->abonos()->sum('valor');
    }

    // Estado legible (Activo, Cerrado, etc.)
    public function getEstadoNombreAttribute()
    {
        return $this->estado ? $this->estado->nombre : null;
    }

    // 🔹 Devoluciones registradas para este plan
    public function devoluciones()
    {
        return $this->hasMany(DevolucionPlanSepare::class, 'plan_separe_id');
    }

}
