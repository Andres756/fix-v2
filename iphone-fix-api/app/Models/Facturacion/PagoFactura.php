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
    public $timestamps = false;

    protected $fillable = [
        'factura_id',
        'forma_pago_id',
        'valor',
        'referencia_externa',
        'observaciones',
        'usuario_id'
    ];

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
