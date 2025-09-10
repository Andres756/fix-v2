<?php
// app/Http/Requests/PlanSepare/StorePlanSepareRequest.php
namespace App\Http\Requests\PlanSepare;
use Illuminate\Foundation\Http\FormRequest;

class StorePlanSepareRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return [
      'cliente_id' => ['required','integer','exists:clientes,id'],
      'inventario_id' => ['required','integer','exists:inventarios,id'],
      'inventario_id_asignado' => ['nullable','integer','exists:inventarios,id'],
      'cambio_equipo' => ['nullable','boolean'],
      'precio_total' => ['required','numeric','min:0'],
      'porcentaje_minimo' => ['nullable','numeric','min:0','max:100'],
      'abono_inicial' => ['nullable','numeric','min:0'],
      'monto_devuelto' => ['nullable','numeric','min:0'],
      'estado_id' => ['required','integer','exists:estados_plan_separe,id'],
      'observaciones' => ['nullable','string'],
    ];
  }
}
