<?php
// app/Http/Requests/Inventario/Proveedores/StoreProveedorRequest.php
namespace App\Http\Requests\Inventario\Proveedores;
use Illuminate\Foundation\Http\FormRequest;

class StoreProveedorRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return [
      'nombre' => ['required','string','max:255'],
      'contacto_nombre' => ['nullable','string','max:255'],
      'telefono' => ['nullable','string','max:50'],
      'correo' => ['nullable','email','max:100'],
      'direccion' => ['nullable','string','max:255'],
    ];
  }
}
