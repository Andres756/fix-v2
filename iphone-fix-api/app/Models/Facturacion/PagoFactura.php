<?php

namespace App\Models\Facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Parametros\FormaPago;
use App\Models\User;

class PagoFactura extends Model
{
    use HasFactory;

    protected $table = 'pagos_factura';
    public $timestamps = true;

    protected $fillable = [
        'factura_id',
        'forma_pago_id',
        'valor',
        'observaciones',
        'usuario_id',               // quién registró el pago
        'estado',
        'motivo_anulacion_id',
        'anulado_por_id',           // quién lo anuló
        'anulado_at',               // cuándo lo anuló
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'anulado_at' => 'datetime',
    ];

    public function motivoAnulacion()
    {
        return $this->belongsTo(MotivoAnulacionPago::class, 'motivo_anulacion_id');
    }

    public function anuladoPor()
    {
        return $this->belongsTo(User::class, 'anulado_por_id');
    }

    // --- Relaciones ---
    public function factura()
    {
        return $this->belongsTo(Factura::class, 'factura_id');
    }

    public function formaPago()
    {
        return $this->belongsTo(FormaPago::class, 'forma_pago_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
