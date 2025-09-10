<?php
// app/Http/Requests/Ventas/Facturas/UpdateDetalleFacturaRequest.php
namespace App\Http\Requests\Ventas\Facturas;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDetalleFacturaRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return [
      'tipo_item_id' => ['sometimes','integer','exists:tipos_item_factura,id'],
      'referencia_id' => ['sometimes','integer','min:1'],
      'descripcion' => ['sometimes','nullable','string'],
      'cantidad' => ['sometimes','integer','min:1'],
      'precio_unitario' => ['sometimes','numeric','min:0'],
      'descuento' => ['sometimes','numeric','min:0'],
      'impuesto' => ['sometimes','numeric','min:0'],
      'total' => ['sometimes','numeric','min:0'],
    ];
  }
}
