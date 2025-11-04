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

    // RELACIONES ACTUALIZADAS
    public function categoria()       { return $this->belongsTo(Categoria::class, 'categoria_id'); }
    public function estado()          { return $this->belongsTo(EstadoInventario::class, 'estado_inventario_id'); }
    public function tipo()            { return $this->belongsTo(TipoDeInventario::class, 'tipo_inventario_id'); }

    // Alias para compatibilidad
    public function estadoInventario() { return $this->estado(); }
    public function tipoInventario()   { return $this->tipo();   }

    // Detalles por tipo
    public function detalleEquipo()   { return $this->hasOne(DetalleEquipo::class, 'inventario_id'); }
    public function detalleProducto() { return $this->hasOne(DetalleProducto::class, 'inventario_id'); }
    public function detalleRepuesto() { return $this->hasOne(DetalleRepuesto::class, 'inventario_id'); }

    // NUEVAS RELACIONES - Movimientos de inventario
    public function entradas()        { return $this->hasMany(EntradaProducto::class, 'inventario_id'); }
    public function salidas()         { return $this->hasMany(SalidaProducto::class, 'inventario_id'); }

    // ACCESSOR para imagen
    public function getImagenUrlAttribute(): ?string
    {
        $path = $this->ruta_imagen;
        if (!$path) {
            return null;
        }

        // Si ya viene absoluta, la respetamos
        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }

        // Generar URL pública desde el disco 'public'
        return Storage::disk('public')->url($path);
    }
}