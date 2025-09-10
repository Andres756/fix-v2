<?php
// app/Http/Requests/Ventas/Descuentos/UpdateDescuentoRequest.php
namespace App\Http\Requests\Ventas\Descuentos;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDescuentoRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return [
      'nombre' => ['sometimes','string','max:100'],
      'tipo_descuento_id' => ['sometimes','integer','exists:tipos_descuento,id'],
      'aplica_a_id' => ['sometimes','integer','exists:aplicacion_descuento,id'],
      'valor' => ['sometimes','numeric','min:0'],
      'activo' => ['sometimes','boolean'],
    ];
  }
}
