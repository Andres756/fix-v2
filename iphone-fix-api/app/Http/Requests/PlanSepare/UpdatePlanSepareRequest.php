<?php
// app/Http/Requests/PlanSepare/UpdatePlanSepareRequest.php
namespace App\Http\Requests\PlanSepare;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanSepareRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return [
      'cliente_id' => ['sometimes','integer','exists:clientes,id'],
      'inventario_id' => ['sometimes','integer','exists:inventarios,id'],
      'inventario_id_asignado' => ['sometimes','nullable','integer','exists:inventarios,id'],
      'cambio_equipo' => ['sometimes','boolean'],
      'precio_total' => ['sometimes','numeric','min:0'],
      'porcentaje_minimo' => ['sometimes','numeric','min:0','max:100'],
      'abono_inicial' => ['sometimes','numeric','min:0'],
      'monto_devuelto' => ['sometimes','numeric','min:0'],
      'estado_id' => ['sometimes','integer','exists:estados_plan_separe,id'],
      'observaciones' => ['sometimes','nullable','string'],
    ];
  }
}
