<?php
// app/Models/Inventario/EntradaProducto.php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventario\Proveedor;
use App\Models\Inventario\Lote;
use App\Models\Inventario\EstadoEntrada;
use App\Models\Inventario\MotivoMovimiento;
use App\Models\Cliente;
use App\Models\User;

class EntradaProducto extends Model
{
    protected $table = 'entradas_producto';

    protected $fillable = [
        'proveedor_id',
        'cliente_id',
        'lote_id',
        'motivo_ingreso_id',
        'estado_entrada_id',
        'tipo_entrada',
        'fecha_entrada',
        'observaciones',
        'usuario_id',
        'total_entrada',
    ];

    protected $casts = [
        'fecha_entrada' => 'date',
        'total_entrada' => 'decimal:2',
    ];

    // Relaciones
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function motivoIngreso()
    {
        return $this->belongsTo(MotivoMovimiento::class, 'motivo_ingreso_id');
    }

    public function estadoEntrada()
    {
        return $this->belongsTo(EstadoEntrada::class, 'estado_entrada_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function items()
    {
        return $this->hasMany(EntradaProductoItem::class, 'entrada_id');
    }

    // Accessors
    public function getOrigenAttribute()
    {
        if ($this->tipo_entrada === 'proveedor' && $this->proveedor) {
            return [
                'tipo' => 'proveedor',
                'id' => $this->proveedor->id,
                'nombre' => $this->proveedor->nombre,
                'nit' => $this->proveedor->nit ?? null,
            ];
        }
        
        if ($this->tipo_entrada === 'cliente' && $this->cliente) {
            return [
                'tipo' => 'cliente',
                'id' => $this->cliente->id,
                'nombre' => $this->cliente->nombre,
                'documento' => $this->cliente->documento ?? null,
            ];
        }
        
        return null;
    }

    // Scopes
    public function scopePorEstado($query, $estadoId)
    {
        return $query->where('estado_entrada_id', $estadoId);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_entrada', $tipo);
    }

    public function scopePorProveedor($query, $proveedorId)
    {
        return $query->where('tipo_entrada', 'proveedor')
                     ->where('proveedor_id', $proveedorId);
    }

    public function scopePorCliente($query, $clienteId)
    {
        return $query->where('tipo_entrada', 'cliente')
                     ->where('cliente_id', $clienteId);
    }

    public function scopePorFecha($query, $fechaInicio, $fechaFin = null)
    {
        if ($fechaFin) {
            return $query->whereBetween('fecha_entrada', [$fechaInicio, $fechaFin]);
        }
        return $query->whereDate('fecha_entrada', $fechaInicio);
    }
}