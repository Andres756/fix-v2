<?php
// app/Http/Requests/Inventario/Entradas/UpdateEntradaProductoRequest.php
namespace App\Http\Requests\Inventario\Entradas;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEntradaProductoRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return [
      'inventario_id' => ['sometimes','integer','exists:inventarios,id'],
      'lote_id' => ['sometimes','nullable','integer','exists:lotes,id'],
      'motivo_ingreso_id' => ['sometimes','nullable','integer','exists:motivos_ingreso,id'],
      'cantidad' => ['sometimes','integer','min:1'],
      'fecha_entrada' => ['sometimes','nullable','date'],
      'notas' => ['sometimes','nullable','string'],
    ];
  }
}
