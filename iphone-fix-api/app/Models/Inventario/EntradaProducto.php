<?php
// app/Models/Inventario/EntradaProducto.php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventario\Proveedor;
use App\Models\Inventario\Cliente;  // Asegúrate de tener el modelo Cliente
use App\Models\Inventario\Lote;
use App\Models\Parametros\MotivoIngreso;

class EntradaProducto extends Model
{
    protected $table = 'entradas_producto';

    protected $fillable = [
        'proveedor_id',       // Mantén el proveedor_id
        'cliente_id',         // Agregar el cliente_id
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

    // Relación para el cliente (nuevo campo)
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');  // Relación con Cliente
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
