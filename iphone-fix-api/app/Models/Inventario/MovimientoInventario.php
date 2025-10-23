<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';
    
    protected $fillable = [
        'inventario_id',
        'motivo_movimiento_id',
        'cantidad',
        'stock_anterior',
        'stock_nuevo',
        'costo_unitario',
        'referencia',
        'observaciones',
        'usuario_id',
    ];
    
    protected $casts = [
        'cantidad' => 'integer',
        'stock_anterior' => 'integer',
        'stock_nuevo' => 'integer',
        'costo_unitario' => 'decimal:2',
    ];
    
    /**
     * Relación con inventario
     */
    public function inventario(): BelongsTo
    {
        return $this->belongsTo(Inventario::class);
    }
    
    /**
     * Relación con motivo de movimiento
     */
    public function motivoMovimiento(): BelongsTo
    {
        return $this->belongsTo(MotivoMovimiento::class);
    }
    
    /**
     * Relación con usuario (opcional)
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }
    
    /**
     * Scope: Solo entradas
     */
    public function scopeEntradas($query)
    {
        return $query->whereHas('motivoMovimiento', fn($q) => $q->where('tipo', 'entrada'));
    }
    
    /**
     * Scope: Solo salidas
     */
    public function scopeSalidas($query)
    {
        return $query->whereHas('motivoMovimiento', fn($q) => $q->where('tipo', 'salida'));
    }
    
    /**
     * Scope: Por rango de fechas
     */
    public function scopeEntreFechas($query, $desde, $hasta)
    {
        return $query->whereBetween('created_at', [$desde, $hasta]);
    }
    
    /**
     * Accessor: Tipo de movimiento
     */
    public function getTipoAttribute(): ?string
    {
        return $this->motivoMovimiento?->tipo;
    }
    
    /**
     * Accessor: Nombre del motivo
     */
    public function getMotivoNombreAttribute(): ?string
    {
        return $this->motivoMovimiento?->nombre;
    }
}