<?php
// app/Models/Inventario/ModeloEquipo.php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModeloEquipo extends Model
{
    use HasFactory;

    protected $table = 'modelos_equipos';

    protected $fillable = [
        'nombre',
        'marca',
        'familia',
        'anio_lanzamiento',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'anio_lanzamiento' => 'integer',
    ];

    // ===========================
    // RELACIONES
    // ===========================
    
    /**
     * Un modelo tiene muchos detalles de equipos (inventarios)
     */
    public function detallesEquipos()
    {
        return $this->hasMany(DetalleEquipo::class, 'modelo_equipo_id');
    }

    /**
     * Obtener inventarios que usan este modelo
     */
    public function inventarios()
    {
        return $this->hasManyThrough(
            Inventario::class,
            DetalleEquipo::class,
            'modelo_equipo_id', // FK en detalles_equipos
            'id',               // FK en inventarios
            'id',               // PK en modelos_equipos
            'inventario_id'     // PK en detalles_equipos
        );
    }

    // ===========================
    // SCOPES
    // ===========================
    
    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }

    public function scopePorMarca($query, $marca)
    {
        return $query->where('marca', $marca);
    }

    public function scopePorFamilia($query, $familia)
    {
        return $query->where('familia', $familia);
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
              ->orWhere('marca', 'like', "%{$termino}%")
              ->orWhere('familia', 'like', "%{$termino}%");
        });
    }

    // ===========================
    // MÉTODOS HELPER
    // ===========================
    
    /**
     * Obtener cantidad de equipos disponibles de este modelo
     */
    public function getCantidadDisponibleAttribute()
    {
        return $this->inventarios()
            ->where('stock', '>', 0)
            ->where('activo', 1)
            ->count();
    }

    /**
     * Obtener valor total del inventario de este modelo
     */
    public function getValorInventarioAttribute()
    {
        return $this->inventarios()
            ->where('stock', '>', 0)
            ->where('activo', 1)
            ->sum(\DB::raw('costo * stock'));
    }
}