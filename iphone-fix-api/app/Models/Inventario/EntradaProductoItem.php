<?php
// app/Models/Inventario/EntradaProductoItem.php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;

class EntradaProductoItem extends Model
{
    protected $table = 'entradas_producto_items';

    protected $fillable = [
        'entrada_id',
        'inventario_id',
        'cantidad',
        'costo_unitario',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'costo_unitario' => 'decimal:2',
    ];

    // Relaciones
    public function entrada()
    {
        return $this->belongsTo(EntradaProducto::class, 'entrada_id');
    }

    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'inventario_id');
    }
}