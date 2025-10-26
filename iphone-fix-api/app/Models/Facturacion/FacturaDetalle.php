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
        'total'
    ];

    // --- Relaciones ---

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'factura_id');
    }
}
