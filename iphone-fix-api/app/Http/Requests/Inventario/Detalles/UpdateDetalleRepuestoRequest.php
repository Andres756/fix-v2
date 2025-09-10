<?php
// app/Http/Requests/Inventario/Detalles/UpdateDetalleRepuestoRequest.php
namespace App\Http\Requests\Inventario\Detalles;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDetalleRepuestoRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return [
      'modelo_compatible' => ['sometimes','nullable','string','max:100'],
      'tipo_repuesto' => ['sometimes','nullable','string','max:100'],
      'referencia_fabricante' => ['sometimes','nullable','string','max:100'],
      'garantia_meses' => ['sometimes','nullable','integer','min:0'],
    ];
  }
}
