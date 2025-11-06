<?php

namespace App\Http\Controllers\Api\OrdenServicio;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrdenServicio\OrdenServicio\CreateOrdenRequest;
use App\Http\Requests\OrdenServicio\OrdenServicio\UpdateOrdenRequest;
use App\Http\Resources\OrdenServicio\OrdenServicioResource;
use App\Models\OrdenServicio\OrdenServicio;
use App\Models\OrdenServicio\HistorialEstadoOs;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrdenServicioController extends Controller
{
    /**
     * Listar todas las órdenes de un cliente.
     */
    public function index($clienteId)
    {
        $ordenes = \App\Models\OrdenServicio\OrdenServicio::where('cliente_id', $clienteId)
            ->with(['equipos.tareas', 'equipos.repuestosInventario', 'equipos.repuestosExternos'])
            ->latest('id')
            ->get();

        // Calcular totales para cada equipo dentro de las órdenes
        $ordenes->each(function ($orden) {
            $orden->equipos->transform(function ($equipo) {
                $costoActividades = $equipo->tareas->sum('costo_aplicado');
                $costoRepuestos   = $equipo->repuestosInventario->sum('costo_total');
                $costoExternos    = $equipo->repuestosExternos->sum('costo_total');
                $costoReal        = $costoActividades + $costoRepuestos + $costoExternos;

                $equipo->precio_total = $costoReal;
                return $equipo;
            });
        });

        return response()->json(['data' => $ordenes]);
    }

    /**
     * Crear una nueva orden de servicio para un cliente.
     */
    public function store(CreateOrdenRequest $request, $clienteId)
    {
        // validamos que el cliente exista
        $cliente = \App\Models\Cliente::findOrFail($clienteId);

        $orden = OrdenServicio::create(array_merge(
            $request->validated(),
            ['cliente_id' => $cliente->id]
        ));

        return new OrdenServicioResource($orden->load('equipos'));
    }

    /**
     * Mostrar una orden de servicio específica de un cliente.
     */
    public function show($clienteId, $ordenId)
    {
        $orden = OrdenServicio::with(['cliente', 'equipos'])
            ->where('cliente_id', $clienteId)
            ->findOrFail($ordenId);

        return response()->json([
            'data' => $orden
        ]);
    }

    /**
     * Actualizar una orden de servicio.
     */
    public function update(UpdateOrdenRequest $request, $clienteId, $ordenId)
    {
        $cliente = Cliente::findOrFail($clienteId);

        $orden = $cliente->ordenesServicio()->findOrFail($ordenId);

        $orden->update($request->validated());

        return new OrdenServicioResource($orden->fresh('equipos'));
    }

    /**
     * Eliminar una orden de servicio.
     */
    public function destroy($clienteId, $ordenId)
    {
        $cliente = Cliente::findOrFail($clienteId);

        $orden = $cliente->ordenesServicio()->findOrFail($ordenId);

        $orden->delete();

        return response()->json([
            'message' => 'Orden de servicio eliminada correctamente'
        ], 200);
    }

    public function listAll(Request $request)
    {
        // Filtros opcionales: ?q=..., ?estado=..., ?cliente_id=...
        $q          = trim((string) $request->get('q', ''));
        $estado     = $request->get('estado');
        $clienteId  = $request->get('cliente_id');

        $query = OrdenServicio::query()
            // Trae lo necesario para que el front pueda mostrar nombre/documento del cliente
            ->with(['cliente:id,nombre,documento'])
            // Si quieres también equipos en el listado, descomenta:
            // ->with('equipos')
            ->orderByDesc('id');

        if ($estado) {
            $query->where('estado', $estado);
        }

        if ($clienteId) {
            $query->where('cliente_id', $clienteId);
        }

        if ($q !== '') {
            $query->where(function($sub) use ($q) {
                $sub->where('codigo_orden', 'like', "%{$q}%")
                    ->orWhereHas('cliente', function($c) use ($q) {
                        $c->where('nombre', 'like', "%{$q}%")
                        ->orWhere('documento', 'like', "%{$q}%");
                    });
            });
        }

        $ordenes = $query->get();

        return OrdenServicioResource::collection($ordenes);
    }

    /**
     * 🔄 Actualiza el estado de la orden según el estado de sus equipos
     */
    public function actualizarEstado($clienteId, $ordenId)
    {
        try {
            $orden = OrdenServicio::with('equipos')
                ->where('cliente_id', $clienteId)
                ->findOrFail($ordenId);

            // 🟡 Si ya está finalizada, avisamos y no hacemos nada
            if (strtolower(trim($orden->estado)) === 'finalizada') {
                return response()->json([
                    'message' => 'La orden ya se encuentra finalizada.',
                    'estado' => $orden->estado,
                    'fecha_entrega' => $orden->fecha_entrega,
                ], 200);
            }

            if ($orden->equipos->isEmpty()) {
                return response()->json([
                    'message' => 'No hay equipos asociados a esta orden.'
                ], 400);
            }

            // ✅ Permitir ambas formas "finalizado" o "finalizada"
            $todosFinalizados = $orden->equipos->every(function ($e) {
                $estado = strtolower(trim($e->estado));
                return in_array($estado, ['finalizado', 'finalizada']);
            });

            if ($todosFinalizados) {
                $orden->estado = 'finalizada';
                $orden->fecha_entrega = now();
                $orden->save();

                HistorialEstadoOs::create([
                    'orden_id' => $orden->id,
                    'usuario_id' => Auth::id(),
                    'estado_anterior' => $orden->getOriginal('estado'),
                    'estado_nuevo' => 'finalizada',
                    'descripcion' => 'Orden marcada como finalizada automáticamente.',
                ]);

                return response()->json([
                    'message' => 'Orden actualizada a estado Finalizada correctamente',
                    'estado' => $orden->estado,
                    'fecha_entrega' => $orden->fecha_entrega,
                ], 200);
            }

            return response()->json([
                'message' => 'Existen equipos sin finalizar. No se puede cerrar la orden.',
                'estado' => $orden->estado,
            ], 422);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al actualizar el estado de la orden',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
