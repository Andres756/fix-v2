<?php
// app/Models/Ventas/Descuento.php
namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Model;
use App\Models\Parametros\TipoDescuento;
use App\Models\Parametros\AplicacionDescuento;

class Descuento extends Model
{
    protected $table = 'descuentos';
    public $timestamps = false;
    protected $fillable = ['nombre','tipo_descuento_id','aplica_a_id','valor','activo'];
    protected $casts = ['valor'=>'decimal:2','activo'=>'bool'];

    public function tipo(){ return $this->belongsTo(TipoDescuento::class, 'tipo_descuento_id'); }
    public function aplicacion(){ return $this->belongsTo(AplicacionDescuento::class, 'aplica_a_id'); }
}
