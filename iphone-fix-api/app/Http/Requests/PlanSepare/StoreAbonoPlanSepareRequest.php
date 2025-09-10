<?php
// app/Http/Requests/PlanSepare/StoreAbonoPlanSepareRequest.php
namespace App\Http\Requests\PlanSepare;
use Illuminate\Foundation\Http\FormRequest;

class StoreAbonoPlanSepareRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return [
      'plan_id' => ['required','integer','exists:plan_separe,id'],
      'monto' => ['required','numeric','min:0.01'],
      'forma_pago_id' => ['nullable','integer','exists:formas_pago,id'],
      'usuario_id' => ['nullable','integer','exists:users,id'],
      'fecha_abono' => ['nullable','date'],
    ];
  }
}
