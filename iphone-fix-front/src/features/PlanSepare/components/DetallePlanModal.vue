<template>
  <Teleport to="body">
    <transition name="modal">
      <div
        v-if="open"
        class="fixed inset-0 z-[9999] overflow-y-auto"
        @click.self="emit('close')"
      >
        <div
          class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0"
        >
          <!-- Overlay -->
          <div
            class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-50 backdrop-blur-sm"
            @click="emit('close')"
          ></div>

          <!-- Modal -->
          <div
            class="relative inline-block w-full max-w-4xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-xl shadow-2xl z-[10000]"
          >
            <!-- Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                    <svg
                      class="w-6 h-6 text-white"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                      />
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-xl font-bold text-white">Detalle del Plan Separe</h3>
                    <p class="text-sm text-blue-100">
                      {{ plan?.codigo ? `Plan #${plan.id}` : '' }}
                    </p>
                  </div>
                </div>
                <button
                  @click="emit('close')"
                  class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-1 transition-colors"
                >
                  <svg
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"
                    />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Body -->
            <div class="max-h-[calc(100vh-200px)] overflow-y-auto">
              <!-- Loading -->
              <div
                v-if="isLoading"
                class="p-6 flex items-center justify-center min-h-[400px]"
              >
                <div class="text-center">
                  <svg
                    class="animate-spin h-12 w-12 text-blue-600 mx-auto mb-4"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
                    <circle
                      class="opacity-25"
                      cx="12"
                      cy="12"
                      r="10"
                      stroke="currentColor"
                      stroke-width="4"
                    ></circle>
                    <path
                      class="opacity-75"
                      fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    ></path>
                  </svg>
                  <p class="text-sm text-gray-600">Cargando detalle del plan...</p>
                </div>
              </div>

              <!-- Contenido -->
              <div v-else-if="plan" class="p-6 space-y-6">
                <!-- Información general -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <!-- Cliente -->
                  <div
                    class="bg-gradient-to-br from-indigo-50 to-white p-4 rounded-lg border border-indigo-200"
                  >
                    <h4 class="text-sm font-semibold text-gray-700 mb-1">Cliente</h4>
                    <p class="text-lg font-bold text-gray-900">
                      {{ plan.cliente?.nombre }}
                    </p>
                    <p class="text-sm text-gray-600">
                      {{ plan.cliente?.documento }}
                    </p>
                    <p v-if="plan.cliente?.telefono" class="text-sm text-gray-500">
                      📱 {{ plan.cliente.telefono }}
                    </p>
                  </div>

                  <!-- Producto -->
                  <div
                    class="bg-gradient-to-br from-green-50 to-white p-4 rounded-lg border border-green-200"
                  >
                    <h4 class="text-sm font-semibold text-gray-700 mb-1">Producto</h4>
                    <p class="text-lg font-bold text-gray-900">
                      {{ plan.inventario?.nombre }}
                    </p>
                    <p class="text-sm text-gray-600">
                      Código: {{ plan.inventario?.codigo }}
                    </p>
                  </div>
                </div>

                <!-- Estado y Fechas -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-500 mb-1">Estado</p>
                    <EstadoPlanBadge
                      :estadoCodigo="plan.estado?.codigo || ''"
                      :estadoNombre="plan.estado?.nombre || ''"
                    />
                  </div>

                  <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-500 mb-1">Fecha Inicio</p>
                    <p class="text-sm font-medium text-gray-900">
                      {{ formatDate(plan.created_at) }}
                    </p>
                  </div>

                  <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-500 mb-1">Devolución</p>
                      <p class="text-sm font-medium text-gray-900">
                        {{ plan.monto_devuelto
                          ? `$${Number(plan.monto_devuelto).toLocaleString('es-CO')}`
                          : '-' }}
                      </p>

                      <p v-if="plan.usuario_devolucion" class="text-xs text-gray-500">
                        Procesado por: <span class="font-medium">{{ plan.usuario_devolucion }}</span>
                      </p>
                  </div>
                </div>

                <!-- Resumen financiero -->
                <div
                  class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-200"
                >
                  <h4 class="text-lg font-bold text-gray-900 mb-4">
                    💰 Resumen Financiero
                  </h4>
                  <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div>
                      <p class="text-xs text-gray-600 mb-1">Precio Total</p>
                      <p class="text-xl font-bold text-gray-900">
                        ${{ Number(plan.precio_total || 0).toLocaleString('es-CO') }}
                      </p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-600 mb-1">Abonado</p>
                      <p class="text-xl font-bold text-green-600">
                        ${{ totalAbonado.toLocaleString('es-CO') }}
                      </p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-600 mb-1">Saldo Pendiente</p>
                      <p class="text-xl font-bold text-orange-600">
                        ${{ saldoPendiente.toLocaleString('es-CO') }}
                      </p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-600 mb-1">Progreso</p>
                      <p class="text-xl font-bold text-blue-600">
                        {{ progreso.toFixed(1) }}%
                      </p>
                    </div>
                  </div>

                  <div class="mt-4">
                    <div class="flex items-center justify-between text-xs mb-1">
                      <span class="text-gray-600">
                        Mínimo: {{ plan.porcentaje_minimo }}%
                      </span>
                      <span class="text-gray-600">Actual: {{ progreso.toFixed(1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 relative">
                      <div
                        class="absolute h-full border-r-2 border-yellow-500"
                        :style="{ left: `${plan.porcentaje_minimo}%` }"
                      ></div>
                      <div
                        class="h-3 rounded-full transition-all duration-300"
                        :class="getProgressColor(progreso, plan.porcentaje_minimo)"
                        :style="{ width: `${progreso}%` }"
                      ></div>
                    </div>
                  </div>
                </div>

                <!-- Historial de abonos -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                  <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-900">
                      📋 Historial de Abonos
                    </h4>
                  </div>

                  <div v-if="abonos.length > 0" class="divide-y divide-gray-200">
                    <div
                      v-for="abono in abonos"
                      :key="abono.id"
                      class="px-4 py-3 hover:bg-gray-50 transition-colors"
                    >
                      <div class="flex items-center justify-between">
                        <div>
                          <p class="text-sm font-medium text-gray-900">
                            ${{ Number(abono.valor || 0).toLocaleString('es-CO') }}
                          </p>
                          <p class="text-xs text-gray-500">
                            {{ abono.forma_pago?.nombre }} •
                            {{ formatDate(abono.created_at) }}
                          </p>
                          <p
                            v-if="abono.observaciones"
                            class="text-xs text-gray-600 mt-1"
                          >
                            {{ abono.observaciones }}
                          </p>
                        </div>
                        <div class="text-right">
                          <p class="text-xs text-gray-500">Por:</p>
                          <p class="text-xs font-medium text-gray-700">
                            {{ abono.usuario?.name || '—' }}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div v-else class="px-4 py-8 text-center text-gray-500">
                    <p class="text-sm">No hay abonos registrados</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div
              class="flex items-center justify-end space-x-3 px-6 py-4 bg-gray-50 border-t border-gray-200"
            >
              <button
                @click="emit('close')"
                class="px-6 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 font-medium transition-colors"
              >
                Cerrar
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import type { PlanSepare, AbonoPlanSepare } from '../types/planSepare'
import { getPlanSepare, fetchAbonosPlan } from '../api/planSepare'
import EstadoPlanBadge from './EstadoPlanBadge.vue'
import { toast } from 'vue3-toastify'

const props = defineProps<{
  open: boolean
  planId: number | null
}>()

const emit = defineEmits<{ (e: 'close'): void }>()

const plan = ref<PlanSepare | null>(null)
const abonos = ref<AbonoPlanSepare[]>([])
const isLoading = ref(false)

/**
 * 🔄 Cargar detalle
 */
async function loadPlanDetalle() {
  if (!props.planId) return
  isLoading.value = true
  try {
    const [planData, abonosData] = await Promise.all([
      getPlanSepare(props.planId),
      fetchAbonosPlan(props.planId),
    ])
    plan.value = planData
    abonos.value = abonosData
  } catch (error: any) {
    console.error('Error cargando detalle:', error)
    toast.error('Error al cargar el detalle del plan')
    plan.value = null
  } finally {
    isLoading.value = false
  }
}

watch(
  () => props.open,
  (newVal) => {
    if (newVal && props.planId) {
      loadPlanDetalle()
    } else {
      plan.value = null
      abonos.value = []
    }
  }
)

/**
 * 📅 Formatear fecha
 */
function formatDate(dateString?: string): string {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('es-CO', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

/**
 * 📊 Resumen financiero
 */
const totalAbonado = computed(() => {
  if (!abonos.value.length) return 0
  return abonos.value.reduce((sum, abono) => sum + parseFloat(abono.valor || '0'), 0)
})

const saldoPendiente = computed(() => {
  const total = parseFloat(plan.value?.precio_total || '0')
  return Math.max(total - totalAbonado.value, 0)
})

const progreso = computed(() => {
  const total = parseFloat(plan.value?.precio_total || '0')
  if (!total) return 0
  return Math.min((totalAbonado.value / total) * 100, 100)
})

function getProgressColor(porcentaje: number, minimo: number): string {
  if (porcentaje >= 100) return 'bg-green-600'
  if (porcentaje >= minimo) return 'bg-blue-600'
  if (porcentaje >= minimo * 0.5) return 'bg-yellow-500'
  return 'bg-orange-500'
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>
