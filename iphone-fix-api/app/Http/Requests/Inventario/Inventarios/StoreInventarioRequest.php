<?php
// app/Http/Requests/Inventario/Inventarios/StoreInventarioRequest.php
namespace App\Http\Requests\Inventario\Inventarios;
use Illuminate\Foundation\Http\FormRequest;

class StoreInventarioRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array
  {
      return [
          'nombre' => ['required','string','max:255','unique:inventarios,nombre'],
          'codigo' => ['required','string','max:100','unique:inventarios,codigo'],

          'tipo_inventario_id'   => ['required','integer','exists:tipos_de_inventario,id'],
          'categoria_id'         => ['required','integer','exists:categorias,id'],
          'estado_inventario_id' => ['required','integer','exists:estados_inventario,id'],
          'proveedor_id'         => ['required','integer','exists:proveedores,id'],
          'lote_id'              => ['nullable','integer','exists:lotes,id'],

          'stock'        => ['nullable','integer','min:0'],
          'stock_minimo' => ['nullable','integer','min:0'],

          'precio' => ['required','numeric','min:0'],
          'costo'  => ['required','numeric','min:0'],

          'tipo_impuesto'  => ['nullable','in:n/a,porcentaje,fijo'],
          'valor_impuesto' => ['nullable','numeric','min:0'],
          'notas'          => ['nullable','string'],

          // ← NUEVO: campo archivo
          'imagen' => ['nullable','image','mimes:jpeg,jpg,png,webp','max:2048'],

          // Detalles por tipo (si aplican)
          'detalle_equipo.imei_1'            => ['nullable','string','max:100'],
          'detalle_equipo.imei_2'            => ['nullable','string','max:100'],
          'detalle_equipo.estado_fisico'     => ['nullable','string','max:100'],
          'detalle_equipo.version_ios'       => ['nullable','string','max:100'],
          'detalle_equipo.almacenamiento'    => ['nullable','string','max:100'],
          'detalle_equipo.color'             => ['nullable','string','max:100'],

          'detalle_producto.material'        => ['nullable','string','max:100'],
          'detalle_producto.compatibilidad'  => ['nullable','string','max:100'],
          'detalle_producto.tipo_accesorio'  => ['nullable','string','max:100'],

          'detalle_repuesto.modelo_compatible'   => ['nullable','string','max:100'],
          'detalle_repuesto.tipo_repuesto'       => ['nullable','string','max:100'],
          'detalle_repuesto.referencia_fabricante'=> ['nullable','string','max:100'],
          'detalle_repuesto.garantia_meses'      => ['nullable','integer','min:0'],
      ];
  }
}
