<?php

namespace App\Services\Facturacion;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PlanSepare\PlanSepare;
use App\Models\PlanSepare\AbonoPlanSepare;
use App\Models\PlanSepare\EstadoPlanSepare;
use App\Models\PlanSepare\LogPlanSepare;
use App\Models\Facturacion\Factura;
use App\Models\Facturacion\FacturaDetalle;
use App\Models\Facturacion\FacturaAuditoria;
use Exception;

class PlanSepareService
{
    /**
     * Crear un nuevo Plan Separe
     */
    public function crearPlan(array $data, int $usuarioId)
    {
        try {
            // 🚫 Validar usuario autenticado (igual que en registrarAbono)
            if (!$usuarioId) {
                throw new \Exception('No se detectó una sesión activa. Inicie sesión nuevamente.');
            }

            return DB::transaction(function () use ($data, $usuarioId) {

                // 🔎 Validar que el cliente no tenga otro plan activo para el mismo inventario
                $existePlanActivo = PlanSepare::where('cliente_id', $data['cliente_id'])
                    ->where('inventario_id', $data['inventario_id'])
                    ->whereHas('estado', function ($q) {
                        $q->whereNotIn('codigo', ['CER', 'CAN', 'DEV']);
                    })
                    ->exists();

                if ($existePlanActivo) {
                    throw new \Exception('Este cliente ya tiene un Plan Separe activo para este producto.');
                }

                // ✅ Crear el plan (ahora con usuario_id incluido)
                $plan = PlanSepare::create([
                    'cliente_id'        => $data['cliente_id'],
                    'inventario_id'     => $data['inventario_id'],
                    'precio_total'      => $data['precio_total'],
                    'porcentaje_minimo' => $data['porcentaje_minimo'] ?? 30,
                    'abono_inicial'     => $data['abono_inicial'] ?? 0,
                    'estado_id'         => EstadoPlanSepare::where('codigo', 'ACT')->value('id'),
                    'usuario_id'        => $usuarioId, // ✅ agregado igual que en registrarAbono
                    'observaciones'     => $data['observaciones'] ?? null
                ]);

                // 🧾 Log de creación
                LogPlanSepare::create([
                    'plan_separe_id' => $plan->id,
                    'accion'         => 'CREAR',
                    'descripcion'    => 'Plan creado desde API',
                    'usuario_id'     => $usuarioId
                ]);

                // 💰 Si hay abono inicial, registrarlo igual que en registrarAbono
                if (!empty($data['abono_inicial']) && $data['abono_inicial'] > 0) {
                    AbonoPlanSepare::create([
                        'plan_separe_id' => $plan->id,
                        'valor'          => $data['abono_inicial'],
                        'forma_pago_id'  => $data['forma_pago_id'] ?? 1,
                        'usuario_id'     => $usuarioId,
                        'observaciones'  => 'Abono inicial automático al crear el plan.'
                    ]);
                }

                return $plan->load(['cliente', 'inventario', 'estado']);
            });
        } catch (\Exception $e) {
            Log::error('Error creando plan separe: ' . $e->getMessage());
            throw $e;
        }
    }



    /**
     * Registrar un abono a un Plan Separe
     */
    public function registrarAbono(int $planId, array $data, int $usuarioId)
    {
        try {
            if (!$usuarioId) {
                throw new \Exception('No se detectó una sesión activa. Inicie sesión nuevamente.');
            }

            return DB::transaction(function () use ($planId, $data, $usuarioId) {

                $plan = PlanSepare::findOrFail($planId);

                // 🚫 Validar que el valor sea positivo
                if (empty($data['valor']) || $data['valor'] <= 0) {
                    throw new \Exception('El valor del abono debe ser mayor a cero.');
                }

                // 💰 Calcular total abonado actual
                $totalActual = AbonoPlanSepare::where('plan_separe_id', $plan->id)->sum('valor');
                $precioTotal = $plan->precio_total;
                $nuevoTotal = $totalActual + $data['valor'];

                // 🚫 Validar que el total no exceda el precio total del plan
                if ($nuevoTotal > $precioTotal) {
                    $saldoRestante = $precioTotal - $totalActual;
                    throw new \Exception("El abono excede el total del plan. Saldo disponible para abonar: {$saldoRestante}.");
                }

                // ✅ Crear abono
                $abono = AbonoPlanSepare::create([
                    'plan_separe_id' => $plan->id,
                    'valor'          => $data['valor'],
                    'forma_pago_id'  => $data['forma_pago_id'],
                    'usuario_id'     => $usuarioId,
                    'observaciones'  => $data['observaciones'] ?? null
                ]);

                // 🧾 Log manual (el trigger también deja registro)
                LogPlanSepare::create([
                    'plan_separe_id' => $plan->id,
                    'accion'         => 'ABONO',
                    'descripcion'    => "Abono registrado desde API. Total abonado: {$nuevoTotal} / {$precioTotal}",
                    'usuario_id'     => $usuarioId
                ]);

                return $abono->load('formaPago');
            });
        } catch (Exception $e) {
            Log::error('Error registrando abono: ' . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Cambiar estado del Plan Separe (cancelar, devolver, reasignar, cerrar)
     */
    public function cambiarEstado(int $planId, int $estadoId, ?string $observaciones = null, ?int $usuarioId = null)
    {
        try {
            if (!$usuarioId) {
                throw new \Exception('No se detectó una sesión activa. Inicie sesión nuevamente.');
            }

            return DB::transaction(function () use ($planId, $estadoId, $observaciones, $usuarioId) {

                // 🔹 Buscar el plan
                $plan = PlanSepare::find($planId);
                if (!$plan) {
                    throw new \Exception("No se encontró el Plan Separe con ID {$planId}.");
                }

                // 🔹 Buscar el estado manualmente (sin findOrFail)
                $estado = EstadoPlanSepare::find($estadoId);
                if (!$estado) {
                    throw new \Exception("El estado con ID {$estadoId} no existe en la base de datos.");
                }

                // 🔹 Validar flujo lógico (opcional)
                $estadoActual = $plan->estado->codigo ?? null;
                if ($estadoActual === 'CER' && $estado->codigo !== 'CER') {
                    throw new \Exception('No se puede modificar un Plan Separe que ya fue cerrado.');
                }

                // 🔹 Actualizar el estado del plan
                $plan->estado_id = $estadoId;
                $plan->observaciones = $observaciones ?? $plan->observaciones;
                $plan->updated_at = now();
                $plan->save();

                // 🔹 Registrar log
                LogPlanSepare::create([
                    'plan_separe_id' => $plan->id,
                    'accion'         => 'CAMBIO_ESTADO',
                    'descripcion'    => "Estado cambiado manualmente a {$estado->nombre}.",
                    'usuario_id'     => $usuarioId
                ]);

                return $plan;
            });

        } catch (\Exception $e) {
            Log::error('Error cambiando estado del Plan Separe: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Consultar historial (logs) de un plan
     */
    public function obtenerHistorial(int $planId)
    {
        return LogPlanSepare::where('plan_separe_id', $planId)
            ->orderByDesc('created_at')
            ->with('usuario')
            ->get();
    }

    /**
     * Consultar todos los abonos de un plan
     */
    public function obtenerAbonos(int $planId)
    {
        return AbonoPlanSepare::where('plan_separe_id', $planId)
            ->with(['formaPago', 'usuario'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Consultar resumen de planes con totales
     */
    public function listarPlanes()
    {
        return PlanSepare::with(['cliente', 'inventario', 'estado'])
            ->withSum('abonos as total_abonos', 'valor')
            ->orderByDesc('created_at')
            ->get();
    }
}
