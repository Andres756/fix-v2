<?php

namespace App\Models\OrdenServicio;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Inventario\Inventario;

class RepuestoOsInventario extends Model
{
    use HasFactory;

    protected $table = 'repuestos_os_inventario';

    protected $fillable = [
        'equipo_os_id',
        'inventario_id',
        'cantidad',
        'costo_unitario_aplicado',
        'costo_total',
        'fecha_uso',
        'observaciones'
    ];

    public function equipo()
    {
        return $this->belongsTo(EquipoOrdenServicio::class, 'equipo_os_id');
    }

    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'inventario_id');
    }
}
