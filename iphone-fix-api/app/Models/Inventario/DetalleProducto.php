<?php
// app/Models/Inventario/DetalleProducto.php
namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;

class DetalleProducto extends Model
{
    protected $table = 'detalles_productos';
    public $timestamps = false;
    protected $primaryKey = 'inventario_id';
    public $incrementing = false;
    protected $fillable = ['inventario_id','material','compatibilidad','tipo_accesorio'];
    public function inventario(){ return $this->belongsTo(Inventario::class, 'inventario_id'); }
}
