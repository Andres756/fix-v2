<?php

namespace App\Http\Controllers\Api\OrdenServicio;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrdenServicio\Repuesto\CreateRepuestoInventarioRequest;
use App\Http\Requests\OrdenServicio\Repuesto\UpdateRepuestoInventarioRequest;
use App\Http\Resources\OrdenServicio\RepuestoOsInventarioResource;
use App\Models\OrdenServicio\EquipoOrdenServicio;
use App\Models\OrdenServicio\RepuestoOsInventario;
use App\Models\Inventario\Inventario;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class RepuestoOsInventarioController extends Controller
{
    /**
     * Tipo de inventario para repuestos
     */
    private const TIPO_REPUESTO = 3;

    public function index($clienteId, $ordenId, $equipoId)
    {
        $equipo = $this->findEquipo($clienteId, $ordenId, $equipoId);

        return RepuestoOsInventarioResource::collection(
            $equipo->repuestosInventario()->with('inventario')->get()
        );
    }

    public function store(CreateRepuestoInventarioRequest $request, $clienteId, $ordenId, $equipoId)
    {
        try {
            DB::beginTransaction();

            $equipo = $this->findEquipo($clienteId, $ordenId, $equipoId);

            // Obtener el inventario con lock para evitar condiciones de carrera
            $inventario = Inventario::where('id', $request->inventario_id)
                ->where('tipo_inventario_id', self::TIPO_REPUESTO)
                ->lockForUpdate()
                ->first();

            if (!$inventario) {
                DB::rollBack();
                return $this->errorResponse('El repuesto seleccionado no existe o no es válido.', 404);
            }

            // Validación final de stock (protección contra condiciones de carrera)
            if ($request->cantidad > $inventario->stock) {
                DB::rollBack();
                return $this->errorResponse(
                    'Stock insuficiente',
                    "Solo quedan {$inventario->stock} unidades disponibles.",
                    400
                );
            }

            // Crear el repuesto
            $repuesto = $this->createRepuesto($equipo, $request, $inventario);

            // Descontar del inventario
            $inventario->decrement('stock', $request->cantidad);

            // Refrescar el modelo para obtener el costo_total calculado por MySQL
            $repuesto->refresh();

            DB::commit();

            return new RepuestoOsInventarioResource($repuesto->load('inventario'));

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error al procesar la solicitud', $e->getMessage());
        }
    }

    public function show($clienteId, $ordenId, $equipoId, $repuestoId)
    {
        $repuesto = $this->findRepuesto($clienteId, $ordenId, $equipoId, $repuestoId);

        return new RepuestoOsInventarioResource($repuesto);
    }

    public function update(UpdateRepuestoInventarioRequest $request, $clienteId, $ordenId, $equipoId, $repuestoId)
    {
        try {
            DB::beginTransaction();

            $repuesto = $this->findRepuesto($clienteId, $ordenId, $equipoId, $repuestoId, false);
            $cantidadOriginal = $repuesto->cantidad;
            
            // Lock del inventario para evitar condiciones de carrera
            $inventario = $repuesto->inventario()->lockForUpdate()->first();

            // Si se está cambiando la cantidad
            if ($request->has('cantidad') && $request->cantidad != $cantidadOriginal) {
                $this->updateCantidad($repuesto, $inventario, $request->cantidad, $cantidadOriginal);
            }

            // Actualizar otros campos
            if ($request->has('observaciones')) {
                $repuesto->observaciones = $request->observaciones;
            }

            $repuesto->save();

            // Refrescar para obtener el costo_total recalculado
            $repuesto->refresh();

            DB::commit();

            return new RepuestoOsInventarioResource($repuesto->load('inventario'));

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error al actualizar', $e->getMessage());
        }
    }

    public function destroy($clienteId, $ordenId, $equipoId, $repuestoId)
    {
        try {
            DB::beginTransaction();

            $repuesto = $this->findRepuesto($clienteId, $ordenId, $equipoId, $repuestoId, false);

            // Devolver la cantidad al inventario
            $repuesto->inventario->increment('stock', $repuesto->cantidad);

            $repuesto->delete();

            DB::commit();

            return response()->json([
                'message' => 'Repuesto eliminado y stock devuelto al inventario'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error al eliminar', $e->getMessage());
        }
    }

    /**
     * Buscar equipo con validaciones
     */
    private function findEquipo($clienteId, $ordenId, $equipoId): EquipoOrdenServicio
    {
        return EquipoOrdenServicio::where('orden_id', $ordenId)
            ->whereHas('orden', fn($q) => $q->where('cliente_id', $clienteId))
            ->findOrFail($equipoId);
    }

    /**
     * Buscar repuesto con validaciones
     */
    private function findRepuesto($clienteId, $ordenId, $equipoId, $repuestoId, $withInventario = true): RepuestoOsInventario
    {
        $query = RepuestoOsInventario::where('equipo_os_id', $equipoId)
            ->whereHas('equipo.orden', fn($q) => $q->where('id', $ordenId)->where('cliente_id', $clienteId));

        if ($withInventario) {
            $query->with('inventario');
        }

        return $query->findOrFail($repuestoId);
    }

    /**
     * Crear repuesto
     */
    private function createRepuesto(EquipoOrdenServicio $equipo, CreateRepuestoInventarioRequest $request, Inventario $inventario): RepuestoOsInventario
    {
        // El costo unitario se toma del precio del inventario
        $costoUnitario = $inventario->precio;

        return $equipo->repuestosInventario()->create([
            'inventario_id' => $request->inventario_id,
            'cantidad' => $request->cantidad,
            'costo_unitario_aplicado' => $costoUnitario, // Precio del inventario
            // costo_total se calcula automáticamente: costo_unitario_aplicado * cantidad
            'fecha_uso' => now(),
            'observaciones' => $request->observaciones,
        ]);
    }

    /**
     * Actualizar cantidad y manejar stock
     */
    private function updateCantidad(RepuestoOsInventario $repuesto, Inventario $inventario, int $nuevaCantidad, int $cantidadOriginal): void
    {
        $diferencia = $nuevaCantidad - $cantidadOriginal;
        
        // Si necesita más cantidad, verificar stock ANTES de descontar
        if ($diferencia > 0) {
            // Verificar que hay suficiente stock disponible
            if ($diferencia > $inventario->stock) {
                throw new \Exception("Stock insuficiente. Solo quedan {$inventario->stock} unidades disponibles para incrementar.");
            }
            
            // Descontar la diferencia del stock
            $inventario->decrement('stock', $diferencia);
            
        } elseif ($diferencia < 0) {
            // Si reduce la cantidad, devolver stock al inventario
            $cantidadADevolver = abs($diferencia);
            $inventario->increment('stock', $cantidadADevolver);
        }
        // Si $diferencia == 0, no hacer nada con el stock

        // Actualizar solo la cantidad - costo_total se recalcula automáticamente
        $repuesto->cantidad = $nuevaCantidad;
    }

    /**
     * Respuesta de error estandarizada
     */
    private function errorResponse(string $error, string $message, int $code = 500): JsonResponse
    {
        return response()->json([
            'error' => $error,
            'message' => $message
        ], $code);
    }
}