<template>
  <Teleport to="body">
    <transition name="modal">
      <div
        v-if="open"
        class="fixed inset-0 z-[9999] overflow-y-auto"
        @click.self="emit('close')"
      >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
          <!-- Overlay -->
          <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-50 backdrop-blur-sm" @click="emit('close')"></div>

          <!-- Modal -->
          <div class="relative inline-block w-full max-w-2xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-xl shadow-2xl z-[10000]">
            <!-- Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-red-600 to-red-700">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-xl font-bold text-white">Anular Plan Separe</h3>
                    <p class="text-sm text-red-100">{{ plan?.codigo || 'Cargando...' }}</p>
                  </div>
                </div>
                <button
                  @click="emit('close')"
                  class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-1 transition-colors"
                >
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Body -->
            <form @submit.prevent="handleSubmit" class="p-6">
              <!-- Loading State -->
              <div v-if="isLoadingData" class="flex items-center justify-center min-h-[300px]">
                <div class="text-center">
                  <svg class="animate-spin h-12 w-12 text-red-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <p class="text-sm text-gray-600">Cargando información...</p>
                </div>
              </div>

              <!-- Form Content -->
              <div v-else-if="plan" class="space-y-6">
                <!-- Alerta de advertencia -->
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                  <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                      <p class="text-sm font-semibold text-red-800">⚠️ Acción irreversible</p>
                      <p class="text-sm text-red-700 mt-1">
                        Está a punto de anular este plan separe. Esta acción no se puede deshacer.
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Información del Plan -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                  <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                      <p class="text-gray-600">Cliente:</p>
                      <p class="font-semibold text-gray-900">{{ plan.cliente?.nombre }}</p>
                    </div>
                    <div>
                      <p class="text-gray-600">Producto:</p>
                      <p class="font-semibold text-gray-900">{{ plan.inventario?.nombre }}</p>
                    </div>
                    <div>
                      <p class="text-gray-600">Precio Total:</p>
                      <p class="font-bold text-gray-900">
                        ${{ Number(plan.precio_total || 0).toLocaleString('es-CO') }}
                      </p>
                    </div>
                    <div>
                      <p class="text-gray-600">Total Abonado:</p>
                      <p class="font-bold text-green-600">
                        ${{ Number(plan.total_abonado || 0).toLocaleString('es-CO') }}
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Devolución -->
                <div v-if="plan.total_abonado && plan.total_abonado > 0" class="space-y-4">
                  <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-4">💰 Configuración de Devolución</h4>
                    
                    <!-- Porcentaje de devolución -->
                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Porcentaje de Devolución
                      </label>
                      <div class="flex items-center space-x-4">
                        <input
                          v-model.number="form.porcentaje_devolucion"
                          type="range"
                          min="0"
                          max="100"
                          step="5"
                          class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                        />
                        <div class="flex items-center space-x-2">
                          <input
                            v-model.number="form.porcentaje_devolucion"
                            type="number"
                            min="0"
                            max="100"
                            class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-center"
                          />
                          <span class="text-gray-600">%</span>
                        </div>
                      </div>
                      <div class="flex items-center justify-between mt-2 text-xs text-gray-500">
                        <span>Sin devolución</span>
                        <span>Devolución completa</span>
                      </div>
                    </div>

                    <!-- Cálculo de devolución -->
                    <div class="mt-4 bg-gradient-to-br from-purple-50 to-pink-50 p-4 rounded-lg border border-purple-200">
                      <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                          <p class="text-gray-600">Monto a devolver:</p>
                          <p class="text-2xl font-bold text-purple-700">
                            ${{ montoDevolucion.toLocaleString('es-CO') }}
                          </p>
                        </div>
                        <div>
                          <p class="text-gray-600">Porcentaje:</p>
                          <p class="text-2xl font-bold text-purple-700">{{ form.porcentaje_devolucion }}%</p>
                        </div>
                      </div>
                    </div>

                    <!-- Forma de Pago para devolución -->
                    <div class="mt-4">
                      <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Forma de Devolución *
                      </label>
                      <select
                        v-model="form.forma_pago_id"
                        required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                      >
                        <option value="">Seleccione método de devolución</option>
                        <option v-for="fp in formasPago" :key="fp.id" :value="fp.id">
                          {{ fp.nombre }}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- Sin abonos -->
                <div v-else class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                  <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                      <p class="text-sm font-semibold text-yellow-800">Sin abonos registrados</p>
                      <p class="text-sm text-yellow-700 mt-1">
                        Este plan no tiene abonos, por lo que no se realizará devolución.
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Motivo -->
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Motivo de Anulación *
                  </label>
                  <textarea
                    v-model="form.motivo"
                    rows="3"
                    required
                    placeholder="Explique el motivo de la anulación..."
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"
                  ></textarea>
                </div>

                <!-- Observaciones adicionales -->
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Observaciones Adicionales
                  </label>
                  <textarea
                    v-model="form.observaciones"
                    rows="2"
                    placeholder="Información adicional (opcional)..."
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"
                  ></textarea>
                </div>

                <!-- Confirmación -->
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                  <label class="flex items-start space-x-3 cursor-pointer">
                    <input
                      v-model="confirmacion"
                      type="checkbox"
                      class="mt-1 w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
                    />
                    <span class="text-sm text-gray-700">
                      Confirmo que deseo anular este plan separe. Entiendo que esta acción es irreversible
                      <span v-if="montoDevolucion > 0">
                        y se procesará una devolución de <strong class="text-red-700">${{ montoDevolucion.toLocaleString('es-CO') }}</strong>
                      </span>.
                    </span>
                  </label>
                </div>
              </div>

              <!-- Footer -->
              <div v-if="!isLoadingData && plan" class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                <button
                  type="button"
                  @click="emit('close')"
                  class="px-6 py-2.5 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 font-medium transition-colors"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  :disabled="isSubmitting || !confirmacion || !form.motivo || (plan.total_abonado > 0 && !form.forma_pago_id)"
                  class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
                >
                  <svg v-if="isSubmitting" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span>{{ isSubmitting ? 'Anulando...' : 'Anular Plan Separe' }}</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { toast } from 'vue3-toastify'
import type { PlanSepare, AnularPlanSeparePayload } from '../types/planSepare'
import { getPlanSepare, anularPlanSepare } from '../api/planSepare'
import { fetchFormasPago } from '../../Facturacion/api/facturacion'

const props = defineProps<{
  open: boolean
  planId: number | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'success'): void
}>()

const plan = ref<PlanSepare | null>(null)
const formasPago = ref<any[]>([])
const isSubmitting = ref(false)
const isLoadingData = ref(false)
const confirmacion = ref(false)

const form = ref<AnularPlanSeparePayload>({
  motivo: '',
  porcentaje_devolucion: 100,
  forma_pago_id: 0,
  observaciones: ''
})

const montoDevolucion = computed(() => {
  if (!plan.value?.total_abonado) return 0
  return Math.round((plan.value.total_abonado * form.value.porcentaje_devolucion) / 100)
})

async function loadData() {
  if (!props.planId) return

  isLoadingData.value = true
  try {
    const [planData, formasPagoData] = await Promise.all([
      getPlanSepare(props.planId),
      fetchFormasPago()
    ])

    plan.value = planData
    formasPago.value = formasPagoData
  } catch (error: any) {
    console.error('Error cargando datos:', error)
    toast.error('Error al cargar los datos del plan')
    plan.value = null
  } finally {
    isLoadingData.value = false
  }
}

async function handleSubmit() {
  if (!props.planId || !form.value.motivo) {
    toast.warning('Complete todos los campos requeridos')
    return
  }

  if (!confirmacion.value) {
    toast.warning('Debe confirmar la anulación')
    return
  }

  if ((plan.value?.total_abonado || 0) > 0 && !form.value.forma_pago_id) {
    toast.warning('Seleccione la forma de devolución')
    return
  }

  isSubmitting.value = true

  try {
    await anularPlanSepare(props.planId, form.value)
    
    toast.success('Plan separe anulado exitosamente')
    emit('success')
    resetForm()
    emit('close')
  } catch (error: any) {
    console.error('Error anulando plan:', error)
    toast.error(error.message || 'Error al anular el plan separe')
  } finally {
    isSubmitting.value = false
  }
}

function resetForm() {
  form.value = {
    motivo: '',
    porcentaje_devolucion: 100,
    forma_pago_id: 0,
    observaciones: ''
  }
  confirmacion.value = false
}

watch(() => props.open, (newVal) => {
  if (newVal && props.planId) {
    loadData()
  } else {
    plan.value = null
    resetForm()
  }
})
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