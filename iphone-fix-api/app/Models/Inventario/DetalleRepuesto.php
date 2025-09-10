<?php
// app/Models/Inventario/DetalleRepuesto.php
namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;

class DetalleRepuesto extends Model
{
    protected $table = 'detalles_repuestos';
    public $timestamps = false;
    protected $primaryKey = 'inventario_id';
    public $incrementing = false;
    protected $fillable = ['inventario_id','modelo_compatible','tipo_repuesto','referencia_fabricante','garantia_meses'];
    public function inventario(){ return $this->belongsTo(Inventario::class, 'inventario_id'); }
}
