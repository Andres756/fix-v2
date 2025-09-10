<?php
// app/Http/Requests/Ventas/Facturas/UpdateFacturaRequest.php
namespace App\Http\Requests\Ventas\Facturas;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFacturaRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return [
      'cliente_id' => ['sometimes','integer','exists:clientes,id'],
      'usuario_id' => ['sometimes','integer','exists:users,id'],
      'tipo_venta_id' => ['sometimes','integer','exists:tipos_venta,id'],
      'estado_id' => ['sometimes','integer','exists:estados_factura,id'],
      'total' => ['sometimes','numeric','min:0'],
      'forma_pago_id' => ['sometimes','nullable','integer','exists:formas_pago,id'],
      'observaciones' => ['sometimes','nullable','string'],
    ];
  }
}
