<?php

namespace App\Http\Controllers\Api\OrdenServicio;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrdenServicio\OrdenServicio\CreateOrdenRequest;
use App\Http\Requests\OrdenServicio\OrdenServicio\UpdateOrdenRequest;
use App\Http\Resources\OrdenServicio\OrdenServicioResource;
use App\Models\OrdenServicio\OrdenServicio;
use App\Models\Cliente;
use Illuminate\Http\Request;

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
        $cliente = Cliente::findOrFail($clienteId);

        $orden = $cliente->ordenesServicio()
            ->with(['equipos.tareas', 'equipos.repuestosInventario', 'equipos.repuestosExternos'])
            ->findOrFail($ordenId);

        return new OrdenServicioResource($orden);
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
}
