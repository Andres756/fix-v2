<?php

namespace App\Models\Facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaDetalle extends Model
{
    use HasFactory;

    protected $table = 'factura_detalle';
    public $timestamps = false;

    protected $fillable = [
        'factura_id',
        'tipo_item',
        'referencia_id',
        'descripcion',
        'cantidad',
        'valor_unitario',
        'descuento',
        'impuesto',
        'total',
        'entregado',
        'estado_id'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'valor_unitario' => 'float',
        'descuento' => 'float',
        'impuesto' => 'float',
        'total' => 'float',
        'entregado' => 'boolean',
        'estado_id'
    ];

    // --- Relaciones ---

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'factura_id');
    }

        public function estado()
    {
        // Relación al catálogo de estados (estados_factura)
        return $this->belongsTo(EstadoFactura::class, 'estado_id');
    }
}
