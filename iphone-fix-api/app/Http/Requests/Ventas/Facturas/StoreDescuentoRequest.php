<?php
// app/Http/Requests/Ventas/Descuentos/StoreDescuentoRequest.php
namespace App\Http\Requests\Ventas\Descuentos;
use Illuminate\Foundation\Http\FormRequest;

class StoreDescuentoRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return [
      'nombre' => ['required','string','max:100'],
      'tipo_descuento_id' => ['required','integer','exists:tipos_descuento,id'],
      'aplica_a_id' => ['required','integer','exists:aplicacion_descuento,id'],
      'valor' => ['required','numeric','min:0'],
      'activo' => ['nullable','boolean'],
    ];
  }
}
