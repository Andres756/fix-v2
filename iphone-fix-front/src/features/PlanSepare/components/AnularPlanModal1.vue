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
            class="relative inline-block w-full max-w-2xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-xl shadow-2xl z-[10000]"
          >
            <!-- Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-red-600 to-red-700">
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
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                      />
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-xl font-bold text-white">Anular Plan Separe</h3>
                    <p class="text-sm text-red-100">
                      {{ plan?.inventario?.codigo || 'Cargando...' }}
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
            <div v-if="isLoading" class="p-8 flex items-center justify-center min-h-[400px]">
              <div class="text-center">
                <svg
                  class="animate-spin h-12 w-12 text-red-600 mx-auto mb-4"
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
                <p class="text-sm text-gray-600">Cargando información...</p>
              </div>
            </div>

            <div v-else-if="plan" class="p-6 space-y-5">
              <!-- Advertencia -->
              <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-start space-x-3">
                <svg
                  class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                  />
                </svg>
                <div>
                  <p class="text-sm font-semibold text-red-800">⚠️ Acción irreversible</p>
                  <p class="text-sm text-red-700">
                    Esta acción anulará permanentemente el plan separe y no se podrá deshacer.
                  </p>
                </div>
              </div>

              <!-- Info del Plan -->
              <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 space-y-3">
                <div class="grid grid-cols-2 gap-4 text-sm">
                  <div>
                    <p class="text-gray-600 mb-1">Cliente</p>
                    <p class="font-semibold text-gray-900">{{ plan.cliente?.nombre }}</p>
                  </div>
                  <div>
                    <p class="text-gray-600 mb-1">Producto</p>
                    <p class="font-semibold text-gray-900">{{ plan.inventario?.nombre }}</p>
                  </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 pt-3 border-t border-gray-300">
                  <div>
                    <p class="text-gray-600 text-sm mb-1">Precio Total</p>
                    <p class="font-bold text-lg text-gray-900">
                      ${{ Number(plan.precio_total || 0).toLocaleString('es-CO') }}
                    </p>
                  </div>
                  <div>
                    <p class="text-gray-600 text-sm mb-1">Total Abonado</p>
                    <p class="font-bold text-lg text-green-600">
                      ${{ totalAbonado.toLocaleString('es-CO') }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Formulario -->
              <form @submit.prevent="handleSubmit" class="space-y-4">
                <!-- Motivo de Anulación -->
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Motivo de Anulación *
                  </label>
                  <select
                    v-model="form.motivo_anulacion_id"
                    required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                  >
                    <option value="">Seleccione un motivo</option>
                    <option
                      v-for="motivo in motivosAnulacion"
                      :key="motivo.id"
                      :value="motivo.id"
                    >
                      {{ motivo.nombre }}
                    </option>
                  </select>
                </div>

                <!-- Porcentaje de Devolución (solo si hay abonos) -->
                <div v-if="totalAbonado > 0">
                  <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Porcentaje de Devolución
                  </label>
                  <div class="relative">
                    <input
                      v-model.number="form.porcentaje_devolucion"
                      type="number"
                      min="0"
                      max="100"
                      step="1"
                      placeholder="0"
                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent pr-10"
                    />
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">
                      %
                    </span>
                  </div>
                  <p class="text-sm text-gray-600 mt-1">
                    Ingrese el porcentaje del monto abonado que se devolverá (0-100)
                  </p>
                  
                  <!-- Monto calculado -->
                  <div
                    v-if="montoDevolucion > 0"
                    class="mt-3 p-3 bg-orange-50 border border-orange-200 rounded-lg"
                  >
                    <p class="text-sm text-gray-700">
                      <span class="font-semibold">Monto a devolver:</span>
                      <span class="text-lg font-bold text-orange-600 ml-2">
                        ${{ montoDevolucion.toLocaleString('es-CO') }}
                      </span>
                    </p>
                  </div>
                </div>

                <!-- Forma de Pago (solo si porcentaje > 0) -->
                <div v-if="form.porcentaje_devolucion > 0">
                  <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Forma de Pago *
                  </label>
                  <select
                    v-model="form.forma_pago_id"
                    required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                  >
                    <option value="">Seleccione la forma de pago</option>
                    <option
                      v-for="formaPago in formasPago"
                      :key="formaPago.id"
                      :value="formaPago.id"
                    >
                      {{ formaPago.nombre }}
                    </option>
                  </select>
                </div>

                <!-- Observaciones -->
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Observaciones
                  </label>
                  <textarea
                    v-model="form.observaciones"
                    rows="3"
                    placeholder="Información adicional sobre la anulación..."
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"
                  ></textarea>
                </div>

                <!-- Checkbox de Confirmación -->
                <div class="flex items-start space-x-2 pt-2">
                  <input
                    id="confirmar"
                    v-model="form.confirmado"
                    type="checkbox"
                    required
                    class="mt-1 rounded border-gray-300 text-red-600 focus:ring-red-500"
                  />
                  <label for="confirmar" class="text-sm text-gray-700">
                    Confirmo que deseo anular este plan separe. Entiendo que esta acción es irreversible y se realizarán los ajustes correspondientes en el inventario y registros contables.
                  </label>
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                  <button
                    type="button"
                    @click="emit('close')"
                    class="px-6 py-2.5 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 font-medium transition-colors"
                  >
                    Cancelar
                  </button>
                  <button
                    type="submit"
                    :disabled="isSubmitting || !form.confirmado"
                    class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
                  >
                    <svg
                      v-if="isSubmitting"
                      class="animate-spin h-5 w-5 text-white"
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
                    <span>{{ isSubmitting ? 'Anulando...' : 'Anular Plan' }}</span>
                  </button>
                </div>
              </form>
            </div>

            <div v-else class="p-6 text-center text-gray-600">
              <p>No se pudo cargar la información del plan.</p>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>


<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { toast } from 'vue3-toastify'
import type { PlanSepare, MotivoAnulacion } from '../types/planSepare'
import type { FormaPago } from '../../Facturacion/types/factura'
import { getPlanSepare, fetchMotivosAnulacion, anularPlanSepare } from '../api/planSepare'
import { fetchFormasPago } from '../../Facturacion/api/facturacion'

const props = defineProps<{
  open: boolean
  planId: number | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'success'): void
}>()

// 🧩 Estados de control
const isLoading = ref(false)
const isSubmitting = ref(false)
const plan = ref<PlanSepare | null>(null)
const motivosAnulacion = ref<MotivoAnulacion[]>([])
const formasPago = ref<FormaPago[]>([])

// 📋 Formulario
const form = ref({
  motivo_anulacion_id: '',
  porcentaje_devolucion: 0,
  forma_pago_id: '',
  observaciones: '',
  confirmado: false
})

// 💰 Cálculos
const totalAbonado = computed(() => {
  if (!plan.value?.abonos?.length) return 0
  return plan.value.abonos.reduce(
    (sum, abono) => sum + parseFloat(String(abono.valor || '0')),
    0
  )
})

const montoDevolucion = computed(() => {
  return Math.round((totalAbonado.value * form.value.porcentaje_devolucion) / 100)
})

/**
 * 🔄 Cargar datos iniciales
 */
async function loadData() {
  if (!props.planId) return
  
  isLoading.value = true
  try {
    // Cargar en paralelo
    const [planData, motivosData, formasPagoData] = await Promise.all([
      getPlanSepare(props.planId),
      fetchMotivosAnulacion(),
      fetchFormasPago()
    ])
    
    plan.value = planData
    motivosAnulacion.value = motivosData
    formasPago.value = formasPagoData
  } catch (error: any) {
    console.error('Error cargando datos:', error)
    toast.error('No se pudo cargar la información necesaria.')
    plan.value = null
  } finally {
    isLoading.value = false
  }
}

/**
 * ❌ Manejar anulación del plan
 */
async function handleSubmit() {
  if (!props.planId) return

  // Validaciones
  if (!form.value.motivo_anulacion_id) {
    toast.warning('Debe seleccionar un motivo de anulación')
    return
  }

  if (form.value.porcentaje_devolucion > 0 && !form.value.forma_pago_id) {
    toast.warning('Debe seleccionar una forma de pago para la devolución')
    return
  }

  if (!form.value.confirmado) {
    toast.warning('Debe confirmar la anulación antes de continuar')
    return
  }

  // Preparar payload
  const payload = {
    motivo_anulacion_id: parseInt(form.value.motivo_anulacion_id),
    porcentaje_devolucion: form.value.porcentaje_devolucion,
    forma_pago_id: form.value.forma_pago_id ? parseInt(form.value.forma_pago_id) : null,
    observaciones: form.value.observaciones || null
  }

  isSubmitting.value = true
  try {
    await anularPlanSepare(props.planId, payload)
    toast.success('✅ Plan separe anulado correctamente')
    emit('success')
    emit('close')
  } catch (error: any) {
    console.error('Error anulando plan:', error)
    toast.error(error?.response?.data?.message || 'Error al anular el plan')
  } finally {
    isSubmitting.value = false
  }
}

/**
 * 🔁 Reiniciar datos del formulario
 */
function resetForm() {
  form.value = {
    motivo_anulacion_id: '',
    porcentaje_devolucion: 0,
    forma_pago_id: '',
    observaciones: '',
    confirmado: false
  }
  plan.value = null
  motivosAnulacion.value = []
  formasPago.value = []
}

/**
 * 👁️ Monitorear apertura de la modal
 */
watch(() => props.open, (newVal) => {
  if (newVal && props.planId) {
    loadData()
  } else {
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
