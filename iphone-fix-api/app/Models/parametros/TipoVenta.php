<?php

namespace App\Models\Parametros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Facturacion\Factura;

class TipoVenta extends Model
{
    use HasFactory;

    protected $table = 'tipos_venta';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion'
    ];

    // --- Relaciones ---

    public function facturas()
    {
        return $this->hasMany(Factura::class, 'tipo_venta_id');
    }
}
