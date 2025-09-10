<?php
// app/Models/Inventario/Categoria.php
namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Parametros\TipoDeInventario;

class Categoria extends Model
{
    use HasFactory;
    protected $table = 'categorias';
    protected $fillable = ['nombre','descripcion','tipo_inventario_id'];
    public function tipoInventario(){ return $this->belongsTo(TipoDeInventario::class, 'tipo_inventario_id'); }
    public function inventarios(){ return $this->hasMany(Inventario::class, 'categoria_id'); }
}
