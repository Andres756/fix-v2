<?php

namespace App\Models\Facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoFactura extends Model
{
    use HasFactory;

    protected $table = 'estados_factura';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'codigo',
        'activo'
    ];

    public function facturas()
    {
        return $this->hasMany(Factura::class, 'estado_id');
    }
}
