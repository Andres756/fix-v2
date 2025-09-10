<?php
// app/Http/Requests/Ventas/Facturas/StoreDetalleFacturaRequest.php
namespace App\Http\Requests\Ventas\Facturas;
use Illuminate\Foundation\Http\FormRequest;

class StoreDetalleFacturaRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return [
      'factura_id' => ['required','integer','exists:facturas,id'],
      'tipo_item_id' => ['required','integer','exists:tipos_item_factura,id'],
      'referencia_id' => ['required','integer','min:1'],
      'descripcion' => ['nullable','string'],
      'cantidad' => ['nullable','integer','min:1'],
      'precio_unitario' => ['nullable','numeric','min:0'],
      'descuento' => ['nullable','numeric','min:0'],
      'impuesto' => ['nullable','numeric','min:0'],
      'total' => ['nullable','numeric','min:0'],
    ];
  }
}
