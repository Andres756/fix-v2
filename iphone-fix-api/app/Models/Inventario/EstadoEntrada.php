<?php
// app/Models/Inventario/EstadoEntrada.php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;

class EstadoEntrada extends Model
{
    protected $table = 'estados_entrada';

    protected $fillable = [
        'nombre',
        'descripcion',
        'codigo',
        'color',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden', 'asc');
    }

    // Relaciones
    public function entradas()
    {
        return $this->hasMany(EntradaProducto::class, 'estado_entrada_id');
    }
}