<?php
// app/Http/Requests/PlanSepare/StorePlanSepareLogRequest.php
namespace App\Http\Requests\PlanSepare;
use Illuminate\Foundation\Http\FormRequest;

class StorePlanSepareLogRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return [
      'plan_id' => ['required','integer','exists:plan_separe,id'],
      'tipo_cambio_id' => ['required','integer','exists:tipos_cambio_plan_separe,id'],
      'inventario_anterior_id' => ['nullable','integer','exists:inventarios,id'],
      'inventario_nuevo_id' => ['nullable','integer','exists:inventarios,id'],
      'precio_anterior' => ['nullable','numeric','min:0'],
      'precio_nuevo' => ['nullable','numeric','min:0'],
      'monto_devuelto' => ['nullable','numeric','min:0'],
      'observaciones' => ['nullable','string'],
      'usuario_id' => ['nullable','integer','exists:users,id'],
      'fecha' => ['nullable','date'],
    ];
  }
}
