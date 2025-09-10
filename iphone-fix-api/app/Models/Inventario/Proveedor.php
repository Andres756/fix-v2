<?php

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
        'contacto_nombre',
        'telefono',
        'correo',
        'direccion'
    ];

    protected $casts = [
        'creado_en' => 'datetime',
        'actualizado_en' => 'datetime',
    ];

    // Relaciones
    public function inventarios()
    { 
        return $this->hasMany(Inventario::class, 'proveedor_id'); 
    }
    
    public function lotes()
    { 
        return $this->hasMany(Lote::class, 'proveedor_id'); 
    }

    public function repuestosExternos()
    {
        return $this->hasMany(RepuestoOsExterno::class, 'proveedor_id');
    }
}