<?php
// app/Http/Requests/Inventario/Detalles/UpdateDetalleEquipoRequest.php
namespace App\Http\Requests\Inventario\Detalles;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDetalleEquipoRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return [
      'imei_1' => ['sometimes','string','max:100'],
      'imei_2' => ['sometimes','nullable','string','max:100'],
      'estado_fisico' => ['sometimes','nullable','string','max:50'],
      'version_ios' => ['sometimes','nullable','string','max:50'],
      'almacenamiento' => ['sometimes','nullable','string','max:50'],
      'color' => ['sometimes','nullable','string','max:50'],
    ];
  }
}
