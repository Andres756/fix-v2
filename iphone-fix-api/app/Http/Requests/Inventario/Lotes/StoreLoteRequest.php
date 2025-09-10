<?php
// app/Http/Requests/Inventario/Lotes/StoreLoteRequest.php
namespace App\Http\Requests\Inventario\Lotes;
use Illuminate\Foundation\Http\FormRequest;

class StoreLoteRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return [
      'codigo_lote' => ['required','string','max:100','unique:lotes,codigo_lote'],
      'proveedor_id' => ['nullable','integer','exists:proveedores,id'],
      'costo_envio' => ['nullable','numeric','min:0'],
      'fecha_ingreso' => ['nullable','date'],
      'notas' => ['nullable','string'],
    ];
  }
}
