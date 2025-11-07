<?php
// app/Models/Inventario/Inventario.php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\Parametros\EstadoInventario;
use App\Models\Parametros\TipoDeInventario;

class Inventario extends Model
{
    use HasFactory;

    protected $table = 'inventarios';

    protected $fillable = [
        'nombre',
        'nombre_detallado',
        'codigo',
        'categoria_id',
        'estado_inventario_id',
        'tipo_inventario_id',
        'stock',
        'reservado',
        'stock_minimo',
        'precio',
        'costo',
        'costo_mayor',
        'tipo_impuesto',
        'valor_impuesto',
        'notas',
        'ruta_imagen',
        'activo', // 🔹 importante si no lo tenías en fillable
    ];

    protected $casts = [
        'stock'          => 'int',
        'stock_minimo'   => 'int',
        'precio'         => 'decimal:2',
        'costo'          => 'decimal:2',
        'costo_mayor'    => 'decimal:2',
        'valor_impuesto' => 'decimal:2',
    ];

    protected $appends = ['imagen_url'];

    // =========================
    // 🔹 RELACIONES ACTUALIZADAS
    // =========================
    public function categoria()       { return $this->belongsTo(Categoria::class, 'categoria_id'); }
    public function estado()          { return $this->belongsTo(EstadoInventario::class, 'estado_inventario_id'); }
    public function tipo()            { return $this->belongsTo(TipoDeInventario::class, 'tipo_inventario_id'); }

    // Alias de compatibilidad
    public function estadoInventario() { return $this->estado(); }
    public function tipoInventario()   { return $this->tipo(); }

    // Detalles por tipo
    public function detalleEquipo()   { return $this->hasOne(DetalleEquipo::class, 'inventario_id'); }
    public function detalleProducto() { return $this->hasOne(DetalleProducto::class, 'inventario_id'); }
    public function detalleRepuesto() { return $this->hasOne(DetalleRepuesto::class, 'inventario_id'); }

    // Movimientos
    public function entradas() { return $this->hasMany(EntradaProducto::class, 'inventario_id'); }
    public function salidas()  { return $this->hasMany(SalidaProducto::class, 'inventario_id'); }

    // =========================
    // 🔹 ACCESSORS
    // =========================
    public function getImagenUrlAttribute(): ?string
    {
        $path = $this->ruta_imagen;

        if (!$path) {
            return null;
        }

        // Si ya viene con URL absoluta, no tocar
        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }

        // Retornar URL pública del storage
        return Storage::disk('public')->url($path);
    }

    // =========================
    // 🔹 SCOPES PERSONALIZADOS
    // =========================

    /**
     * Trae solo inventarios activos.
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }

    /**
     * Trae solo inventarios tipo 1 (equipos únicos).
     */
    public function scopeUnicos($query)
    {
        return $query->where('tipo_inventario_id', 1);
    }

    /**
     * Trae productos disponibles para venta (activos, con stock > 0 y no reservados).
     */
    public function scopeDisponibles($query)
    {
        return $query
            ->where('activo', 1)
            ->where('stock', '>', 0)
            ->where(function ($q) {
                $q->whereNull('reservado')->orWhere('reservado', 0);
            });
    }

    protected static function booted()
    {
        static::creating(function ($item) {
            $inventario = \App\Models\Inventario\Inventario::find($item->inventario_id);
            if ($inventario && $inventario->tipo_inventario_id == 1 && $inventario->stock > 0) {
                throw new \Exception("No se puede registrar una segunda entrada para el equipo '{$inventario->nombre}' (stock actual: {$inventario->stock}).");
            }
        });
    }

}
