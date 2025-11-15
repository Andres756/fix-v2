<?php
// app/Models/Inventario/Proveedor.php

namespace App\Models\Inventario;

use App\Models\OrdenServicio\RepuestoOsExterno;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proveedor extends Model
{
    use HasFactory;
    
    protected $table = 'proveedores';
    
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';
    
    protected $fillable = [
        'nombre',
        'nit',
        'tipo_documento',
        'contacto_nombre',
        'telefono',
        'correo',
        'direccion'
    ];

    protected $casts = [
        'creado_en' => 'datetime',
        'actualizado_en' => 'datetime',
    ];

    // Scopes para búsqueda
    public function scopeBuscar($query, $termino)
    {
        if (empty($termino)) {
            return $query;
        }

        return $query->where(function($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
              ->orWhere('nit', 'like', "%{$termino}%")
              ->orWhere('contacto_nombre', 'like', "%{$termino}%");
        });
    }

    public function scopeActivos($query)
    {
        return $query->whereNotNull('nombre');
    }

    // Relaciones
    public function inventarios()
    { 
        return $this->hasMany(Inventario::class, 'proveedor_id'); 
    }
    
    public function lotes()
    { 
        return $this->hasMany(Lote::class, 'proveedor_id'); 
    }

    public function entradas()
    {
        return $this->hasMany(EntradaProducto::class, 'proveedor_id');
    }

    public function repuestosExternos()
    {
        return $this->hasMany(RepuestoOsExterno::class, 'proveedor_id');
    }
}