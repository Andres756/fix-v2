<?php
// app/Http/Requests/Inventario/Inventarios/UpdateInventarioRequest.php
namespace App\Http\Requests\Inventario\Inventarios;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInventarioRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return [
      'nombre' => ['sometimes','string','max:255'],
      'nombre_detallado' => ['sometimes','nullable','string','max:255'],
      'codigo' => ['sometimes','nullable','string','max:100'],
      'categoria_id' => ['sometimes','nullable','integer','exists:categorias,id'],
      'lote_id' => ['sometimes','nullable','integer','exists:lotes,id'],
      'estado_inventario_id' => ['sometimes','nullable','integer','exists:estados_inventario,id'],
      'proveedor_id' => ['sometimes','nullable','integer','exists:proveedores,id'],
      'tipo_inventario_id' => ['sometimes','integer','exists:tipos_de_inventario,id'],
      'stock' => ['sometimes','integer','min:0'],
      'stock_minimo' => ['sometimes','integer','min:0'],
      'precio' => ['sometimes','numeric','min:0'],
      'costo' => ['sometimes','numeric','min:0'],
      'costo_mayor' => ['sometimes','numeric','min:0'],
      'tipo_impuesto' => ['sometimes','in:n/a,porcentaje,fijo'],
      'valor_impuesto' => ['sometimes','numeric','min:0'],
      'activo' => ['sometimes','boolean'],
      'fecha_ingreso' => ['sometimes','date'],
      'notas' => ['sometimes','nullable','string'],
      'ruta_imagen' => ['sometimes','nullable','string','max:255'],
    ];
  }
}
