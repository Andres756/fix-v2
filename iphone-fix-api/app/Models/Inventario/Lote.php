<?php
// app/Models/Inventario/Lote.php
namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lote extends Model
{
    use HasFactory;
    protected $table = 'lotes';
    protected $fillable = ['codigo_lote','proveedor_id','costo_envio','fecha_ingreso','notas'];
    protected $casts = ['costo_envio'=>'decimal:2','fecha_ingreso'=>'date'];
    public function proveedor(){ return $this->belongsTo(Proveedor::class, 'proveedor_id'); }
    public function entradas(){ return $this->hasMany(EntradaProducto::class, 'lote_id'); }
    public function inventarios(){ return $this->hasMany(Inventario::class, 'lote_id'); }
}
