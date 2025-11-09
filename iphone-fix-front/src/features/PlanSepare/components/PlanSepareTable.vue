<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">Lista de Planes Separe</h3>
        <button
          @click="emit('create')"
          class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center space-x-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <span>Nuevo Plan</span>
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="flex items-center justify-center py-12">
      <div class="flex flex-col items-center space-y-3">
        <svg class="animate-spin h-10 w-10 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-sm text-gray-500">Cargando planes...</p>
      </div>
    </div>

    <!-- Tabla -->
    <div v-else-if="planes.length > 0" class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Código</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cliente</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Producto</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Precio</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Progreso</th>
<th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
  Estado
</th>

            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fecha creación</th>
            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
          <tr v-for="plan in planes" :key="plan.id" class="hover:bg-gray-50 transition-colors">
            <!-- Código -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">
                {{ plan.codigo || `PLAN-${plan.id}` }}
              </div>
              <div class="text-xs text-gray-500">ID: {{ plan.id }}</div>
            </td>

            <!-- Cliente -->
            <td class="px-6 py-4">
              <div class="text-sm font-medium text-gray-900">
                {{ plan.cliente?.nombre }}
              </div>
              <div class="text-xs text-gray-500">{{ plan.cliente?.documento }}</div>
            </td>

            <!-- Producto -->
            <td class="px-6 py-4">
              <div class="text-sm font-medium text-gray-900">
                {{ plan.inventario?.nombre }}
              </div>
              <div class="text-xs text-gray-500">{{ plan.inventario?.codigo }}</div>
            </td>

            <!-- Precio -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-bold text-gray-900">
                ${{ Number(plan.precio_total || 0).toLocaleString('es-CO') }}
              </div>
              <div class="text-xs text-gray-500">
                Min: {{ plan.porcentaje_minimo }}%
              </div>
            </td>

            <!-- Progreso -->
            <td class="px-6 py-4">
              <div class="space-y-1">
                <div class="flex items-center justify-between text-xs">
                  <span class="text-gray-600">
                    {{ calcularProgreso(plan).toFixed(1) }}%
                  </span>
                  <span class="font-medium text-gray-900">
                    ${{ Number(plan.total_abonos ?? 0).toLocaleString('es-CO') }}
                  </span>
                </div>

                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div
                    class="h-2 rounded-full transition-all duration-300"
                    :class="getProgressColor(
                      calcularProgreso(plan),
                      parseFloat(String(plan.porcentaje_minimo ?? '0'))
                    )"
                    :style="{ width: `${calcularProgreso(plan)}%` }"
                  ></div>
                </div>

                <div class="text-xs text-gray-500">
                  Pendiente:
                  ${{ (
                    parseFloat(String(plan.precio_total ?? '0')) -
                    parseFloat(String(plan.total_abonos ?? '0'))
                  ).toLocaleString('es-CO') }}
                </div>
              </div>
            </td>

            <!-- Estado -->
            <td class="px-6 py-4 whitespace-normal break-words text-center">
              <EstadoPlanBadge
                :estadoCodigo="plan.estado?.codigo || ''"
                :estadoNombre="plan.estado?.nombre || 'Sin estado'"
              />
            </td>

            <!-- Fecha creación -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">
                {{ formatDate(plan.created_at) }}
              </div>
            </td>

            <!-- Acciones -->
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <div class="flex items-center justify-center space-x-2">
                <!-- Ver Detalle -->
                <button
                  @click="emit('view', plan.id)"
                  class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                  title="Ver detalle"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>

                <!-- Registrar Abono -->
                <button
                  v-if="puedeAbonar(plan)"
                  @click="emit('abono', plan.id)"
                  class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                  title="Registrar abono"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </button>

                <!-- Anular -->
                <button
                  v-if="puedeAnular(plan)"
                  @click="emit('anular', plan.id)"
                  class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                  title="Anular plan"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>

                <!-- Reasignar -->
                <button
                  v-if="puedeReasignar(plan)"
                  @click="emit('reasignar', plan.id)"
                  class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-colors"
                  title="Reasignar producto"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Estado vacío -->
    <div v-else class="flex flex-col items-center justify-center py-12 px-4">
      <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <h3 class="text-lg font-medium text-gray-900 mb-2">No hay planes separe</h3>
      <p class="text-gray-500 text-center mb-4">
        Aún no se han creado planes separe. Comienza creando uno nuevo.
      </p>
      <button
        @click="emit('create')"
        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
      >
        Crear primer plan
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { PlanSepare } from '../types/planSepare'
import EstadoPlanBadge from './EstadoPlanBadge.vue'

defineProps<{
  planes: PlanSepare[]
  isLoading: boolean
}>()

const emit = defineEmits<{
  (e: 'create'): void
  (e: 'view', planId: number): void
  (e: 'abono', planId: number): void
  (e: 'anular', planId: number): void
  (e: 'reasignar', planId: number): void
}>()

/**
 * 📅 Formatear fecha de creación del plan
 */
function formatDate(dateString: string): string {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('es-CO', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  })
}

/**
 * 📊 Calcular porcentaje de progreso del plan separe
 */
function calcularProgreso(plan: PlanSepare): number {
  const totalAbonos = parseFloat(String(plan.total_abonos ?? '0'))
  const total = parseFloat(String(plan.precio_total ?? '0'))
  if (!total) return 0
  return Math.min((totalAbonos / total) * 100, 100)
}

/**
 * 🎨 Color del progreso según avance
 */
function getProgressColor(porcentaje: number, minimo: number): string {
  if (porcentaje >= 100) return 'bg-green-600'
  if (porcentaje >= minimo) return 'bg-blue-600'
  if (porcentaje >= minimo * 0.5) return 'bg-yellow-500'
  return 'bg-orange-500'
}

/**
 * ⚙️ Permisos de acciones según estado
 */
function puedeAbonar(plan: PlanSepare): boolean {
  const estadosPermitidos = ['ACT', 'ABIERTO', 'ASE', 'ASEGURADO']
  return estadosPermitidos.includes(plan.estado?.codigo?.toUpperCase() || '')
}

function puedeAnular(plan: PlanSepare): boolean {
  const estadosPermitidos = ['ACT', 'ABIERTO', 'ASE', 'ASEGURADO', 'REA', 'PEN_REA']
  return estadosPermitidos.includes(plan.estado?.codigo?.toUpperCase() || '')
}

function puedeReasignar(plan: PlanSepare): boolean {
  const estadosPermitidos = ['REA', 'PEN_REA']
  return estadosPermitidos.includes(plan.estado?.codigo?.toUpperCase() || '') && !plan.factura_id
}
</script>
