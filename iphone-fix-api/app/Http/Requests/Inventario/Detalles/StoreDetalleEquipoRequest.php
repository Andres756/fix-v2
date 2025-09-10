<?php
// app/Http/Requests/Inventario/Detalles/StoreDetalleEquipoRequest.php
namespace App\Http\Requests\Inventario\Detalles;
use Illuminate\Foundation\Http\FormRequest;

class StoreDetalleEquipoRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return [
      'inventario_id' => ['required','integer','exists:inventarios,id'],
      'imei_1' => ['required','string','max:100'],
      'imei_2' => ['nullable','string','max:100'],
      'estado_fisico' => ['nullable','string','max:50'],
      'version_ios' => ['nullable','string','max:50'],
      'almacenamiento' => ['nullable','string','max:50'],
      'color' => ['nullable','string','max:50'],
    ];
  }
}
