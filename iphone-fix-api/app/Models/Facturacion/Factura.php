<?php

namespace App\Models\Facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Parametros\FormaPago;
use App\Models\Parametros\TipoVenta;
use App\Models\Facturacion\EstadoFactura;
use App\Models\User;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

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
        'consecutivo',
        'entregado',
        'orden_servicio_id',
        'es_prefactura'
    ];

    protected $casts = [
        'subtotal' => 'float',
        'impuestos' => 'float',
        'descuentos' => 'float',
        'total' => 'float',
        'fecha_emision' => 'datetime',
        'entregado' => 'boolean',
        'es_prefactura' => 'boolean',
    ];

    protected $appends = ['total_pagado', 'saldo_pendiente'];

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

    public function tipoVenta()
    {
        return $this->belongsTo(TipoVenta::class, 'tipo_venta_id');
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

    // --- Accesores ---

    public function getTotalPagadoAttribute()
    {
        return $this->pagos()->sum('valor');
    }

    public function getSaldoPendienteAttribute()
    {
        return max(0, $this->total - $this->getTotalPagadoAttribute());
    }

    protected function fechaEmision(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Carbon::parse($value)->format('Y-m-d H:i:s') : null
        );
    }
}
