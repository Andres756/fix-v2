<?php
// app/Http/Requests/Inventario/Proveedores/UpdateProveedorRequest.php
namespace App\Http\Requests\Inventario\Proveedores;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProveedorRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return [
      'nombre' => ['sometimes','string','max:255'],
      'contacto_nombre' => ['sometimes','nullable','string','max:255'],
      'telefono' => ['sometimes','nullable','string','max:50'],
      'correo' => ['sometimes','nullable','email','max:100'],
      'direccion' => ['sometimes','nullable','string','max:255'],
    ];
  }
}
