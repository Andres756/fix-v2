<?php
// app/Http/Requests/Ventas/Facturas/StoreFacturaRequest.php
namespace App\Http\Requests\Ventas\Facturas;
use Illuminate\Foundation\Http\FormRequest;

class StoreFacturaRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return [
      'cliente_id' => ['required','integer','exists:clientes,id'],
      'usuario_id' => ['required','integer','exists:users,id'],
      'tipo_venta_id' => ['required','integer','exists:tipos_venta,id'],
      'estado_id' => ['required','integer','exists:estados_factura,id'],
      'total' => ['required','numeric','min:0'],
      'forma_pago_id' => ['nullable','integer','exists:formas_pago,id'],
      'observaciones' => ['nullable','string'],
    ];
  }
}
