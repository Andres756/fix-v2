<?php

namespace App\Models\Facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Parametros\FormaPago;
use App\Models\User;
use App\Models\Ventas\Cliente;

class Factura extends Model
{
    use HasFactory;

    protected $table = 'facturas';

    protected $fillable = [
        'codigo',
        'cliente_id',
        'usuario_id',
        'tipo_venta_id',
        'forma_pago_id',
        'estado_id',
        'subtotal',
        'impuestos',
        'descuentos',
        'total',
        'observaciones',
        'fecha_emision',
        'prefijo',
        'consecutivo'
    ];

    // --- Relaciones ---

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function formaPago()
    {
        return $this->belongsTo(FormaPago::class, 'forma_pago_id');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoFactura::class, 'estado_id');
    }

    public function detalles()
    {
        return $this->hasMany(FacturaDetalle::class, 'factura_id');
    }

    public function pagos()
    {
        return $this->hasMany(PagoFactura::class, 'factura_id');
    }

    public function auditorias()
    {
        return $this->hasMany(FacturaAuditoria::class, 'factura_id');
    }

    // --- Accesor ---
    public function getTotalPagadoAttribute()
    {
        return $this->pagos()->sum('valor');
    }

    public function getSaldoPendienteAttribute()
    {
        return $this->total - $this->getTotalPagadoAttribute();
    }
}
