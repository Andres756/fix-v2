<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'documento',
        'telefono',
        'correo',
        'direccion',
    ];

    // 🔗 Relación: un cliente tiene muchas órdenes de servicio
    public function ordenesServicio()
    {
        return $this->hasMany(\App\Models\OrdenServicio\OrdenServicio::class, 'cliente_id');
    }

    // 🔗 Relación inversa: un cliente tiene muchas entradas de productos
    public function entradasProducto()
    {
        return $this->hasMany(\App\Models\Inventario\EntradaProducto::class, 'cliente_id');
    }
}
