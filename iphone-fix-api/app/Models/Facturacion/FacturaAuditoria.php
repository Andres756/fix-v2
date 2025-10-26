<?php

namespace App\Models\Facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class FacturaAuditoria extends Model
{
    use HasFactory;

    protected $table = 'factura_auditoria';
    public $timestamps = false;

    protected $fillable = [
        'factura_id',
        'usuario_id',
        'accion',
        'detalle'
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'factura_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
