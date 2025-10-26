<?php

namespace App\Models\Parametros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormaPago extends Model
{
    use HasFactory;

    protected $table = 'formas_pago';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'codigo',
        'activo'
    ];

    // --- Relaciones ---

    public function abonosPlanSepare()
    {
        return $this->hasMany(\App\Models\PlanSepare\AbonoPlanSepare::class, 'forma_pago_id');
    }

    public function facturas()
    {
        return $this->hasMany(\App\Models\Facturacion\Factura::class, 'forma_pago_id');
    }

    public function pagosFacturas()
    {
        return $this->hasMany(\App\Models\Facturacion\PagoFactura::class, 'forma_pago_id');
    }
}
