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
            <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-700">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-xl font-bold text-white">Registrar Abono</h3>
                    <p class="text-sm text-green-100">{{ plan?.codigo || 'Cargando...' }}</p>
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
                  <svg class="animate-spin h-12 w-12 text-green-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <p class="text-sm text-gray-600">Cargando información...</p>
                </div>
              </div>

              <!-- Form Content -->
              <div v-else-if="plan" class="space-y-6">
                <!-- Información del Plan -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-lg border border-blue-200">
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
                      <p class="font-bold text-lg text-gray-900">
                        ${{ Number(plan.precio_total || 0).toLocaleString('es-CO') }}
                      </p>
                    </div>
                    <div>
                      <p class="text-gray-600">Saldo Pendiente:</p>
                      <p class="font-bold text-lg text-orange-600">
                        ${{ Number(plan.saldo_pendiente || 0).toLocaleString('es-CO') }}
                      </p>
                    </div>
                  </div>

                  <!-- Progreso -->
                  <div class="mt-4">
                    <div class="flex items-center justify-between text-xs mb-1">
                      <span class="text-gray-600">Progreso actual</span>
                      <span class="font-semibold text-blue-700">{{ plan.porcentaje_abonado || 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                      <div
                        class="h-2 rounded-full transition-all"
                        :class="getProgressColor(plan.porcentaje_abonado || 0)"
                        :style="{ width: `${Math.min(plan.porcentaje_abonado || 0, 100)}%` }"
                      ></div>
                    </div>
                  </div>
                </div>

                <!-- Valor del Abono -->
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Valor del Abono *
                  </label>
                  <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-500">$</span>
                    <input
                      v-model.number="form.valor"
                      type="number"
                      min="1000"
                      :max="plan.saldo_pendiente"
                      step="1000"
                      required
                      class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      placeholder="0"
                    />
                  </div>
                  <div class="flex items-center justify-between mt-2">
                    <p class="text-xs text-gray-500">
                      Máximo: ${{ Number(plan.saldo_pendiente || 0).toLocaleString('es-CO') }}
                    </p>
                    <button
                      type="button"
                      @click="form.valor = plan.saldo_pendiente"
                      class="text-xs text-green-600 hover:text-green-700 font-medium"
                    >
                      Pagar saldo completo
                    </button>
                  </div>
                </div>

                <!-- Forma de Pago -->
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Forma de Pago *
                  </label>
                  <select
                    v-model="form.forma_pago_id"
                    required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                  >
                    <option value="">Seleccione una forma de pago</option>
                    <option v-for="fp in formasPago" :key="fp.id" :value="fp.id">
                      {{ fp.nombre }}
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
                    placeholder="Notas adicionales sobre el abono..."
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none"
                  ></textarea>
                </div>

                <!-- Simulación -->
                <div v-if="form.valor > 0" class="bg-gradient-to-br from-green-50 to-emerald-50 p-4 rounded-lg border border-green-200">
                  <h4 class="text-sm font-semibold text-gray-900 mb-3">💡 Después de este abono:</h4>
                  <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                      <p class="text-gray-600">Total abonado:</p>
                      <p class="font-bold text-green-700">
                        ${{ ((plan.total_abonado || 0) + form.valor).toLocaleString('es-CO') }}
                      </p>
                    </div>
                    <div>
                      <p class="text-gray-600">Saldo restante:</p>
                      <p class="font-bold text-orange-700">
                        ${{ Math.max(0, (plan.saldo_pendiente || 0) - form.valor).toLocaleString('es-CO') }}
                      </p>
                    </div>
                    <div>
                      <p class="text-gray-600">Porcentaje:</p>
                      <p class="font-bold text-blue-700">{{ nuevoProcentaje }}%</p>
                    </div>
                    <div>
                      <p class="text-gray-600">Estado:</p>
                      <p class="font-bold" :class="getNuevoEstadoClass()">
                        {{ nuevoEstadoNombre }}
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Alerta si completa el plan -->
                <div v-if="nuevoProcentaje >= 100" class="bg-green-100 border border-green-300 rounded-lg p-4">
                  <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                      <p class="text-sm font-semibold text-green-800">¡Plan completado!</p>
                      <p class="text-sm text-green-700 mt-1">
                        Con este abono completarás el 100% del plan. Se generará automáticamente una factura.
                      </p>
                    </div>
                  </div>
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
                  :disabled="isSubmitting || !form.valor || !form.forma_pago_id"
                  class="px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
                >
                  <svg v-if="isSubmitting" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span>{{ isSubmitting ? 'Registrando...' : 'Registrar Abono' }}</span>
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
import type { PlanSepare, RegistrarAbonoPayload } from '../types/planSepare'
import { getPlanSepare, registrarAbono } from '../api/planSepare'
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

const form = ref<RegistrarAbonoPayload>({
  valor: 0,
  forma_pago_id: 0,
  observaciones: ''
})

const nuevoProcentaje = computed(() => {
  if (!plan.value || !form.value.valor) return plan.value?.porcentaje_abonado || 0
  const totalAbonado = (plan.value.total_abonado || 0) + form.value.valor
  return Math.round((totalAbonado / plan.value.precio_total) * 100)
})

const nuevoEstadoNombre = computed(() => {
  if (nuevoProcentaje.value >= 100) return 'Facturado'
  if (nuevoProcentaje.value >= (plan.value?.porcentaje_minimo || 30)) return 'Asegurado'
  return 'Abierto'
})

function getNuevoEstadoClass(): string {
  if (nuevoProcentaje.value >= 100) return 'text-green-700'
  if (nuevoProcentaje.value >= (plan.value?.porcentaje_minimo || 30)) return 'text-blue-700'
  return 'text-yellow-700'
}

function getProgressColor(porcentaje: number): string {
  if (porcentaje >= 100) return 'bg-green-600'
  if (porcentaje >= (plan.value?.porcentaje_minimo || 30)) return 'bg-blue-600'
  return 'bg-yellow-500'
}

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
  if (!props.planId || !form.value.valor || !form.value.forma_pago_id) {
    toast.warning('Complete todos los campos requeridos')
    return
  }

  if (form.value.valor > (plan.value?.saldo_pendiente || 0)) {
    toast.warning('El valor del abono no puede superar el saldo pendiente')
    return
  }

  isSubmitting.value = true

  try {
    await registrarAbono(props.planId, form.value)
    
    toast.success('¡Abono registrado exitosamente!')
    emit('success')
    resetForm()
    emit('close')
  } catch (error: any) {
    console.error('Error registrando abono:', error)
    toast.error(error.message || 'Error al registrar el abono')
  } finally {
    isSubmitting.value = false
  }
}

function resetForm() {
  form.value = {
    valor: 0,
    forma_pago_id: 0,
    observaciones: ''
  }
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