<?php
// app/Http/Requests/Inventario/Categorias/StoreCategoriaRequest.php
namespace App\Http\Requests\Inventario\Categorias;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoriaRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return [
      'nombre' => ['required','string','max:100','unique:categorias,nombre'],
      'descripcion' => ['nullable','string'],
      'tipo_inventario_id' => ['required','integer','exists:tipos_de_inventario,id'],
    ];
  }
  protected function prepareForValidation(): void {
    if ($this->has('nombre')) $this->merge(['nombre'=>trim(mb_strtoupper($this->input('nombre')))]);
  }
}
