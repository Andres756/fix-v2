<?php
// app/Http/Requests/Ventas/Facturas/StoreFacturaLogRequest.php
namespace App\Http\Requests\Ventas\Facturas;
use Illuminate\Foundation\Http\FormRequest;

class StoreFacturaLogRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return [
      'factura_id' => ['required','integer','exists:facturas,id'],
      'accion_id' => ['required','integer','exists:acciones_factura_log,id'],
      'usuario_id' => ['nullable','integer','exists:users,id'],
      'detalle' => ['nullable','string'],
      'created_at' => ['nullable','date'],
    ];
  }
}
