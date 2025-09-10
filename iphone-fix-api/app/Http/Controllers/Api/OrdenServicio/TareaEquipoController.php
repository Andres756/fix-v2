<?php

namespace App\Http\Controllers\Api\OrdenServicio;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrdenServicio\Tarea\CreateTareaRequest;
use App\Http\Requests\OrdenServicio\Tarea\UpdateTareaRequest;
use App\Http\Resources\OrdenServicio\TareaEquipoResource;
use App\Models\OrdenServicio\OrdenServicio;
use App\Models\OrdenServicio\EquipoOrdenServicio;
use App\Models\OrdenServicio\TareaEquipo;
use App\Models\Parametros\TipoTrabajo;

class TareaEquipoController extends Controller
{
    // GET /clientes/{clienteId}/ordenes/{ordenId}/equipos/{equipoId}/tareas
    public function index($clienteId, $ordenId, $equipoId)
    {
        $equipo = $this->resolveEquipo($clienteId, $ordenId, $equipoId);

        $tareas = TareaEquipo::where('equipo_os_id', $equipo->id)
            ->with(['tipoTrabajo' => function ($q) {
                // NO seleccionar "costo": la columna real es costo_aplicado
                $q->select('id', 'nombre', 'costo_sugerido'); // ✅

            }])
            ->latest('id')
            ->get();

        return TareaEquipoResource::collection($tareas);
    }

    // POST /clientes/{clienteId}/ordenes/{ordenId}/equipos/{equipoId}/tareas
    public function store(CreateTareaRequest $request, $clienteId, $ordenId, $equipoId)
    {
        $equipo = $this->resolveEquipo($clienteId, $ordenId, $equipoId);

        $data = $request->validated();
        $data['equipo_os_id'] = $equipo->id;
        $data['estado'] = 'pendiente'; // siempre inicia pendiente

        // 1) Usa el costo enviado (editable en front) si viene
        $costo = array_key_exists('costo_aplicado', $data) ? $data['costo_aplicado'] : null;

        // 2) Si no viene, toma el sugerido del Tipo de Trabajo
        if ($costo === null) {
            $tipo = TipoTrabajo::findOrFail($data['tipo_trabajo_id']);
            $costo = $tipo->costo_sugerido; // columna real
        }

        // 3) Si aún es null, devolvemos 422 (mejor que romper por NOT NULL)
        if ($costo === null) {
            return response()->json([
                'message' => 'El costo no puede ser nulo.',
                'errors' => [
                    'costo_aplicado' => ['Envíe costo_aplicado o configure costo_aplicado en el Tipo de trabajo.']
                ],
            ], 422);
        }

        $data['costo_aplicado'] = (float) $costo;

        $tarea = TareaEquipo::create($data);

        return new TareaEquipoResource(
            $tarea->load(['tipoTrabajo' => function ($q) {
                $q->select('id', 'nombre', 'costo_sugerido');
            }])
        );
    }

    // GET /clientes/{clienteId}/ordenes/{ordenId}/equipos/{equipoId}/tareas/{tareaId}
    public function show($clienteId, $ordenId, $equipoId, $tareaId)
    {
        $this->resolveEquipo($clienteId, $ordenId, $equipoId);

        $tarea = TareaEquipo::where('equipo_os_id', $equipoId)
            ->with(['tipoTrabajo' => function ($q) {
                $q->select('id', 'nombre', 'costo_sugerido');
            }])
            ->findOrFail($tareaId);

        return new TareaEquipoResource($tarea);
    }

    // PUT /clientes/{clienteId}/ordenes/{ordenId}/equipos/{equipoId}/tareas/{tareaId}
    public function update(UpdateTareaRequest $request, $clienteId, $ordenId, $equipoId, $tareaId)
    {
        $this->resolveEquipo($clienteId, $ordenId, $equipoId);

        $tarea = TareaEquipo::where('equipo_os_id', $equipoId)->findOrFail($tareaId);
        $data  = $request->validated();

        // Si cambian el tipo y NO mandan costo_aplicado, recalculamos con costo_aplicado
        if (array_key_exists('tipo_trabajo_id', $data) && !array_key_exists('costo_aplicado', $data)) {
            $tipo = TipoTrabajo::findOrFail($data['tipo_trabajo_id']);
            $data['costo_aplicado'] = (float) ($tipo->costo_sugerido ?? $tarea->costo_aplicado);
        }

        // Blindaje: si llega explícitamente null, conserva el actual (DB es NOT NULL)
        if (array_key_exists('costo_aplicado', $data) && $data['costo_aplicado'] === null) {
            $data['costo_aplicado'] = (float) $tarea->costo_aplicado;
        }

        $tarea->update($data);

        return new TareaEquipoResource(
            $tarea->fresh()->load(['tipoTrabajo' => function ($q) {
                $q->select('id', 'nombre', 'costo_aplicado');
            }])
        );
    }

    // DELETE /clientes/{clienteId}/ordenes/{ordenId}/equipos/{equipoId}/tareas/{tareaId}
    public function destroy($clienteId, $ordenId, $equipoId, $tareaId)
    {
        $this->resolveEquipo($clienteId, $ordenId, $equipoId);

        $tarea = TareaEquipo::where('equipo_os_id', $equipoId)->findOrFail($tareaId);
        $tarea->delete();

        return response()->json(['message' => 'Tarea eliminada'], 200);
    }

    // Helper: valida anidación cliente -> orden -> equipo
    private function resolveEquipo($clienteId, $ordenId, $equipoId): EquipoOrdenServicio
    {
        $orden = OrdenServicio::where('cliente_id', $clienteId)->findOrFail($ordenId);
        return $orden->equipos()->where('id', $equipoId)->firstOrFail();
    }
}
