<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MotivoMovimiento extends Model
{
    protected $table = 'motivos_movimientos';
    
    protected $fillable = [
        'nombre',
        'tipo',
        'codigo',
        'descripcion',
        'activo',
        'orden',
    ];
    
    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];
    
    /**
     * Movimientos que usan este motivo
     */
    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }
    
    /**
     * Scope: Solo motivos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
    
    /**
     * Scope: Solo entradas
     */
    public function scopeEntradas($query)
    {
        return $query->where('tipo', 'entrada');
    }
    
    /**
     * Scope: Solo salidas
     */
    public function scopeSalidas($query)
    {
        return $query->where('tipo', 'salida');
    }
    
    /**
     * Scope: Ordenados
     */
    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden');
    }
    
    /**
     * Obtener motivo por código (con caché)
     */
    public static function porCodigo(string $codigo): ?self
    {
        static $cache = [];
        
        if (!isset($cache[$codigo])) {
            $cache[$codigo] = self::where('codigo', $codigo)->first();
        }
        
        return $cache[$codigo];
    }
    
    /**
     * Helper: ¿Es entrada?
     */
    public function esEntrada(): bool
    {
        return $this->tipo === 'entrada';
    }
    
    /**
     * Helper: ¿Es salida?
     */
    public function esSalida(): bool
    {
        return $this->tipo === 'salida';
    }
}