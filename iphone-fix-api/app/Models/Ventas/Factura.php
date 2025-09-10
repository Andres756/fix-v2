<?php
// app/Models/Ventas/Factura.php
namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Model;
use App\Models\Clientes\Cliente;
use App\Models\User;
use App\Models\Parametros\TipoVenta;
use App\Models\Parametros\EstadoFactura;
use App\Models\Parametros\FormaPago;

class Factura extends Model
{
    protected $table = 'facturas';
    protected $fillable = ['cliente_id','usuario_id','tipo_venta_id','estado_id','total','forma_pago_id','observaciones'];
    protected $casts = ['total'=>'decimal:2'];

    public function cliente(){ return $this->belongsTo(Cliente::class, 'cliente_id'); }
    public function usuario(){ return $this->belongsTo(User::class, 'usuario_id'); }
    public function tipoVenta(){ return $this->belongsTo(TipoVenta::class, 'tipo_venta_id'); }
    public function estado(){ return $this->belongsTo(EstadoFactura::class, 'estado_id'); }
    public function formaPago(){ return $this->belongsTo(FormaPago::class, 'forma_pago_id'); }

    public function detalles(){ return $this->hasMany(DetalleFactura::class, 'factura_id'); }
    public function logs(){ return $this->hasMany(FacturaLog::class, 'factura_id'); }
}
