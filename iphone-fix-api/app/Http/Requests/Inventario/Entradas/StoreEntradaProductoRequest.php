<?php
// app/Http/Requests/Inventario/Entradas/StoreEntradaProductoRequest.php
namespace App\Http\Requests\Inventario\Entradas;
use Illuminate\Foundation\Http\FormRequest;

class StoreEntradaProductoRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return [
      'inventario_id' => ['required','integer','exists:inventarios,id'],
      'lote_id' => ['nullable','integer','exists:lotes,id'],
      'motivo_ingreso_id' => ['nullable','integer','exists:motivos_ingreso,id'],
      'cantidad' => ['required','integer','min:1'],
      'fecha_entrada' => ['nullable','date'],
      'notas' => ['nullable','string'],
    ];
  }
}
