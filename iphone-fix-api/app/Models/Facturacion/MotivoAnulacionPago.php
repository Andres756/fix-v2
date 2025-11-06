<?php

namespace App\Models\Facturacion;

use Illuminate\Database\Eloquent\Model;

class MotivoAnulacionPago extends Model
{
    protected $table = 'motivos_anulacion_pagos';

    protected $fillable = ['nombre', 'descripcion', 'activo'];

    public function pagos()
    {
        return $this->hasMany(PagoFactura::class, 'motivo_anulacion_id');
    }
}
