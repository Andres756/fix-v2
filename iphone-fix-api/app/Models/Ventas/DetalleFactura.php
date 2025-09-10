<?php
// app/Models/Ventas/DetalleFactura.php
namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Model;
use App\Models\Parametros\TipoItemFactura;

class DetalleFactura extends Model
{
    protected $table = 'detalle_factura';
    public $timestamps = false;
    protected $fillable = [
        'factura_id','tipo_item_id','referencia_id','descripcion',
        'cantidad','precio_unitario','descuento','impuesto','total'
    ];
    protected $casts = [
        'cantidad'=>'int','precio_unitario'=>'decimal:2',
        'descuento'=>'decimal:2','impuesto'=>'decimal:2','total'=>'decimal:2'
    ];

    public function factura(){ return $this->belongsTo(Factura::class, 'factura_id'); }
    public function tipoItem(){ return $this->belongsTo(TipoItemFactura::class, 'tipo_item_id'); }

    // Helper (opcional): obtener la entidad referenciada según tipo_item_id
    // public function referencia(){ ...switch para producto/OS/repuesto/plan_separe... }
}
