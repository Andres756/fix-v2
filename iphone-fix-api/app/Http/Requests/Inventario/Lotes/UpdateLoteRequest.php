<?php
// app/Http/Requests/Inventario/Lotes/UpdateLoteRequest.php
namespace App\Http\Requests\Inventario\Lotes;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLoteRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    $id = $this->route('lote');
    return [
      'codigo_lote' => ['sometimes','string','max:100',"unique:lotes,codigo_lote,{$id}"],
      'proveedor_id' => ['sometimes','nullable','integer','exists:proveedores,id'],
      'costo_envio' => ['sometimes','nullable','numeric','min:0'],
      'fecha_ingreso' => ['sometimes','nullable','date'],
      'notas' => ['sometimes','nullable','string'],
    ];
  }
}
