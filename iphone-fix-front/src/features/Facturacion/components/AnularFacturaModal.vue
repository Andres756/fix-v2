<!-- features/Facturacion/components/AnularFacturaModal.vue -->
<template>
  <Teleport to="body">
    <transition
      name="modal"
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
      appear
    >
      <div v-if="open" class="fixed inset-0 z-[9999] overflow-y-auto">
        <!-- Backdrop -->
        <div
          class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
          @click="handleClose"
        ></div>

        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
          <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-gray-900">Anular Factura</h3>
                  <p class="text-sm text-gray-500">Esta acción no se puede deshacer</p>
                </div>
              </div>
              <button
                @click="handleClose"
                :disabled="isLoading"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors disabled:opacity-50"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Loading de verificación -->
            <div v-if="isVerifying" class="p-12">
              <div class="flex flex-col items-center justify-center space-y-4">
                <div class="w-10 h-10 border-4 border-gray-200 border-t-blue-600 rounded-full animate-spin"></div>
                <p class="text-sm text-gray-500">Verificando factura...</p>
              </div>
            </div>

            <!-- Mensaje de error si no se puede anular -->
            <div v-else-if="!canAnular && facturaInfo" class="p-6">
              <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                  <svg class="w-6 h-6 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                  <div>
                    <h4 class="font-semibold text-red-900">No se puede anular esta factura</h4>
                    <p class="mt-1 text-sm text-red-700">{{ errorMessage }}</p>
                  </div>
                </div>
              </div>
              <div class="mt-6 flex justify-end">
                <button
                  @click="handleClose"
                  class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                >
                  Cerrar
                </button>
              </div>
            </div>

            <!-- Formulario de anulación -->
            <form v-else-if="canAnular && facturaInfo" @submit.prevent="handleSubmit" class="p-6">
              <!-- Info de la factura -->
              <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <div class="grid grid-cols-2 gap-3 text-sm">
                  <div>
                    <p class="text-gray-600">Código:</p>
                    <p class="font-semibold text-gray-900">{{ facturaInfo.codigo }}</p>
                  </div>
                  <div>
                    <p class="text-gray-600">Cliente:</p>
                    <p class="font-semibold text-gray-900">{{ facturaInfo.cliente?.nombre || '—' }}</p>
                  </div>
                  <div>
                    <p class="text-gray-600">Total:</p>
                    <p class="font-semibold text-gray-900">{{ formatMoney(facturaInfo.total) }}</p>
                  </div>
                  <div>
                    <p class="text-gray-600">Estado:</p>
                    <p class="font-semibold text-gray-900">{{ facturaInfo.estado?.nombre || '—' }}</p>
                  </div>
                </div>
              </div>

              <!-- Advertencia -->
              <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-start gap-3">
                  <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                  <div class="text-sm">
                    <p class="font-semibold text-yellow-900">Advertencia</p>
                    <ul class="mt-1 text-yellow-700 space-y-1">
                      <li>• Esta acción no se puede deshacer</li>
                      <li>• La factura quedará marcada como ANULADA</li>
                      <li v-if="facturaInfo.pagos && facturaInfo.pagos.length > 0">
                        • Los pagos registrados no serán revertidos automáticamente
                      </li>
                      <li v-if="facturaInfo.orden_servicio_id">
                        • La orden de servicio asociada volverá a estar disponible para facturar
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              <!-- 🧾 Detalles de la factura -->
              <div v-if="facturaInfo?.detalles?.length" class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Seleccione los ítems a anular (opcional)
                </label>
                <div class="overflow-x-auto border rounded-lg">
                  <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                      <tr>
                        <th class="px-4 py-2 text-left w-12">
                          <input
                            type="checkbox"
                            v-model="selectAll"
                            @change="toggleSelectAll"
                            class="w-4 h-4 text-red-600 border-gray-300 rounded"
                          />
                        </th>
                        <th class="px-4 py-2 text-left">Descripción</th>
                        <th class="px-4 py-2 text-right">Cantidad</th>
                        <th class="px-4 py-2 text-right">Precio</th>
                        <th class="px-4 py-2 text-right">Total</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    <tr v-for="detalle in facturaInfo.detalles" :key="detalle.id" 
                        :class="{
                            'opacity-60 line-through text-gray-500 bg-gray-50': detalle.estado?.codigo === 'ANUL'  // Estilo visual para anulados
                        }"
                        class="border-t border-gray-100">
                      <td class="px-4 py-2">
                        <input
                          type="checkbox"
                          v-model="selectedDetalles"
                          :value="detalle.id"
                          :disabled="detalle.estado?.codigo === 'ANUL'"
                          class="w-4 h-4 text-red-600 border-gray-300 rounded"
                        />
                      </td>
                      <td class="px-4 py-2 text-gray-800">{{ detalle.descripcion || detalle.inventario?.nombre || '—' }}</td>
                      <td class="px-4 py-2 text-right">{{ detalle.cantidad }}</td>
                      <td class="px-4 py-2 text-right">{{ formatMoney(detalle.precio_unitario || 0) }}</td>
                      <td class="px-4 py-2 text-right font-medium text-gray-900">
                        {{ formatMoney((detalle.cantidad || 1) * (detalle.precio_unitario || 0)) }}
                      </td>
                    </tr>
                    </tbody>
                  </table>
                </div>
                <p class="mt-2 text-xs text-gray-500">
                  Si no seleccionas ítems, se anulará la factura completa.
                </p>
              </div>

              <!-- Motivo de anulación -->
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Motivo de anulación <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="form.motivo"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                >
                  <option value="">Seleccione un motivo</option>
                  <option value="Error en datos del cliente">Error en datos del cliente</option>
                  <option value="Error en productos/servicios">Error en productos/servicios</option>
                  <option value="Error en precios">Error en precios</option>
                  <option value="Duplicación de factura">Duplicación de factura</option>
                  <option value="Solicitud del cliente">Solicitud del cliente</option>
                  <option value="Devolución de productos">Devolución de productos</option>
                  <option value="Cancelación de servicio">Cancelación de servicio</option>
                  <option value="Error administrativo">Error administrativo</option>
                  <option value="Otro">Otro</option>
                </select>
              </div>

              <!-- Observaciones -->
              <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Observaciones adicionales
                </label>
                <textarea
                  v-model="form.observaciones"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"
                  placeholder="Proporcione detalles adicionales sobre la anulación (opcional)"
                ></textarea>
              </div>

              <!-- Checkbox de confirmación -->
              <div class="mb-6">
                <label class="flex items-start gap-3 cursor-pointer">
                  <input
                    v-model="form.confirmado"
                    type="checkbox"
                    class="mt-1 w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
                  />
                  <span class="text-sm text-gray-700">
                    Confirmo que deseo anular esta factura y entiendo que esta acción no se puede deshacer
                  </span>
                </label>
              </div>

              <!-- Botones -->
              <div class="flex justify-end gap-3">
                <button
                  type="button"
                  @click="handleClose"
                  :disabled="isLoading"
                  class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium disabled:opacity-50"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  :disabled="isLoading || !form.confirmado || !form.motivo"
                  class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium disabled:bg-red-300 disabled:cursor-not-allowed flex items-center gap-2"
                >
                  <svg v-if="isLoading" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ isLoading ? 'Anulando...' : 'Anular Factura' }}
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
import { ref, reactive, watch } from 'vue'
import { toast } from 'vue3-toastify'
import { getFactura, verificarAnulacion, anularFacturaAvanzado } from '../api/facturacion'
import type { Factura } from '../types/factura'

// Props
interface Props {
  open: boolean
  facturaId: number | null
}

const props = defineProps<Props>()

// Emits
const emit = defineEmits<{
  (e: 'close'): void
  (e: 'success'): void
}>()

// Estado
const isVerifying = ref(false)
const isLoading = ref(false)
const canAnular = ref(false)
const errorMessage = ref('')
const facturaInfo = ref<Factura | null>(null)

// Formulario
const form = reactive({
  motivo: '',
  observaciones: '',
  confirmado: false
})

// ✅ Estados para manejar selección de detalles
const selectedDetalles = ref<number[]>([])
const selectAll = ref(false)

// ✅ Marcar o desmarcar todos
const toggleSelectAll = () => {
  if (!facturaInfo.value?.detalles) return
  if (selectAll.value) {
    selectedDetalles.value = facturaInfo.value.detalles.map(d => d.id)
  } else {
    selectedDetalles.value = []
  }
}

// 🔁 Actualizar selectAll cuando cambian selecciones
watch(selectedDetalles, (val) => {
  if (!facturaInfo.value?.detalles) return
  selectAll.value = val.length === facturaInfo.value.detalles.length
})

// 💰 Formato de dinero
const formatMoney = (amount: number): string => {
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount || 0)
}

// ✅ Verificar si se puede anular
const verifyAnulacion = async () => {
  if (!props.facturaId) return
  
  isVerifying.value = true
  canAnular.value = false
  errorMessage.value = ''
  
  try {
    // Obtener info de la factura
    facturaInfo.value = await getFactura(props.facturaId)
    
    // Verificar si se puede anular
    const result = await verificarAnulacion(props.facturaId)
    
    canAnular.value = result.puede_anular
    if (!result.puede_anular) {
      errorMessage.value = result.mensaje || 'No se puede anular esta factura'
    }
  } catch (error: any) {
    console.error('Error verificando anulación:', error)
    canAnular.value = false
    errorMessage.value = error.message || 'Error al verificar la factura'
  } finally {
    isVerifying.value = false
  }
}

// ✅ Enviar anulación avanzada
const handleSubmit = async () => {
  if (!form.motivo) {
    toast.warning('Debe seleccionar un motivo de anulación')
    return
  }

  if (!form.confirmado) {
    toast.warning('Debe confirmar que desea anular la factura')
    return
  }

  try {
    isLoading.value = true

    // 🧩 Filtrar detalles anulados para que no se incluyan en el payload
    const detallesActivos = selectedDetalles.value.filter(id => {
      // Filtrar los detalles anulados que no deben enviarse
      const detalle = factura.value.detalles.find(d => d.id === id)
      return detalle && detalle.estado?.codigo !== 'ANUL'  // Solo los activos
    })

    // Verificar si se seleccionaron detalles válidos
    if (detallesActivos.length === 0) {
      toast.warning('Debe seleccionar al menos un ítem no anulado')
      return
    }

    const payload = {
      motivo: form.motivo,
      detalles: detallesActivos,  // ✅ Solo los detalles activos seleccionados
      acciones: {
        repuestos_internos: 'reutilizables',
        repuestos_externos: 'mantener',
        comision: 'mantener_pago'
      },
      observaciones: form.observaciones || undefined
    }

    const response = await anularFacturaAvanzado(props.facturaId!, payload)

    // ✅ Mostrar mensaje y cerrar modal con retraso breve
    toast.success(response?.message || 'Anulación procesada correctamente')

    setTimeout(() => {
      emit('success')
      handleClose()
    }, 400)

  } catch (error: any) {
    console.error('❌ Error en anulación avanzada:', error)
    const msg =
      error?.response?.data?.message ||
      error?.message ||
      'Error al procesar la anulación'
    toast.error(msg)
  } finally {
    isLoading.value = false
  }
}

// ✅ Cerrar modal y limpiar todo
const handleClose = () => {
  if (isLoading.value) return

  form.motivo = ''
  form.observaciones = ''
  form.confirmado = false

  selectedDetalles.value = []
  selectAll.value = false

  facturaInfo.value = null
  canAnular.value = false
  errorMessage.value = ''

  emit('close')
}

// 🔁 Reaccionar cuando se abre la modal
watch(() => props.open, async (isOpen) => {
  if (isOpen && props.facturaId) {
    await verifyAnulacion()
  }
})
</script>
