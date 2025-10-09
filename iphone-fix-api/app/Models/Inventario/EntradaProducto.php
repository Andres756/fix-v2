<?php
// app/Models/Inventario/EntradaProducto.php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventario\Proveedor;
use App\Models\Inventario\Lote;
use App\Models\Parametros\MotivoIngreso;

class EntradaProducto extends Model
{
    protected $table = 'entradas_producto';

    protected $fillable = [
        'proveedor_id',
        'lote_id',
        'motivo_ingreso_id',
        'fecha_entrada',
        'observaciones',
    ];

    protected $casts = [
        'fecha_entrada' => 'date',
    ];

    // Relaciones
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function motivoIngreso()
    {
        return $this->belongsTo(MotivoIngreso::class, 'motivo_ingreso_id');
    }

    public function items()
    {
        return $this->hasMany(EntradaProductoItem::class, 'entrada_id');
    }
}