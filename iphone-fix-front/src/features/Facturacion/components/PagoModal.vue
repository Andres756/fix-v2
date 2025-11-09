<!-- features/Facturacion/components/PagoModal.vue -->
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
          <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-gray-900">Registrar Pago</h3>
                  <p class="text-sm text-gray-500">Factura {{ facturaInfo?.codigo }}</p>
                </div>
              </div>
              <button
                @click="handleClose"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Resumen de factura -->
            <div v-if="facturaInfo" class="p-6 bg-gray-50 border-b border-gray-200">
              <div class="grid grid-cols-3 gap-4">
                <div>
                  <p class="text-sm text-gray-600">Total Factura</p>
                  <p class="text-xl font-bold text-gray-900">{{ formatMoney(facturaInfo.total) }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-600">Total Pagado</p>
                  <p class="text-xl font-bold text-green-600">{{ formatMoney(facturaInfo.total_pagado || 0) }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-600">Saldo Pendiente</p>
                  <p class="text-xl font-bold" :class="saldoPendiente > 0 ? 'text-orange-600' : 'text-gray-400'">
                    {{ formatMoney(saldoPendiente) }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Formulario de pagos -->
            <form @submit.prevent="handleSubmit" class="p-6">
              <!-- Lista de pagos -->
              <div class="space-y-4 mb-6">
                <div v-for="(pago, index) in pagos" :key="index" 
                     class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                  <div class="flex items-start gap-4">
                    <div class="flex-1 grid grid-cols-2 gap-4">
                      <!-- Forma de pago -->
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                          Forma de Pago *
                        </label>
                        <select 
                          v-model="pago.forma_pago_id" 
                          required
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        >
                          <option value="">Seleccione...</option>
                          <option v-for="forma in formasPago" :key="forma.id" :value="forma.id">
                            {{ forma.nombre }}
                          </option>
                        </select>
                      </div>

                      <!-- Valor -->
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                          Valor *
                        </label>
                        <div class="relative">
                          <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                          <input
                            v-model.number="pago.valor"
                            type="number"
                            min="0.01"
                            step="0.01"
                            required
                            class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="0.00"
                          />
                        </div>
                      </div>

                      <!-- Referencia (opcional) -->
                      <div v-if="needsReference(pago.forma_pago_id)">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                          Referencia/Transacción
                        </label>
                        <input
                          v-model="pago.referencia_externa"
                          type="text"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                          placeholder="Ej: #12345"
                        />
                      </div>

                      <!-- Observaciones (opcional) -->
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                          Observaciones
                        </label>
                        <input
                          v-model="pago.observaciones"
                          type="text"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                          placeholder="Opcional"
                        />
                      </div>
                    </div>

                    <!-- Botón eliminar -->
                    <button
                      v-if="pagos.length > 1"
                      type="button"
                      @click="removePago(index)"
                      class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                      title="Eliminar pago"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Botón agregar pago -->
              <button
                type="button"
                @click="addPago"
                class="mb-6 w-full py-2 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-green-500 hover:text-green-600 transition-colors flex items-center justify-center gap-2"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Agregar forma de pago
              </button>

              <!-- Totales -->
              <div class="p-4 bg-green-50 rounded-lg border border-green-200 mb-6">
                <div class="flex justify-between items-center">
                  <div>
                    <p class="text-sm text-gray-600">Total a pagar</p>
                    <p class="text-2xl font-bold text-gray-900">{{ formatMoney(totalPagos) }}</p>
                  </div>
                  <div class="text-right">
                    <p class="text-sm text-gray-600">Nuevo saldo</p>
                    <p class="text-xl font-bold" :class="nuevoSaldo > 0 ? 'text-orange-600' : 'text-green-600'">
                      {{ formatMoney(nuevoSaldo) }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Monto recibido (para calcular vueltas) -->
              <div v-if="pagoEnEfectivo" class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Monto Recibido (para vueltas)
                </label>
                <div class="flex gap-4">
                  <div class="relative flex-1">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                    <input
                      v-model.number="montoRecibido"
                      type="number"
                      min="0"
                      step="1000"
                      class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                      placeholder="0"
                    />
                  </div>
                  <div v-if="montoRecibido > 0" class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">Vueltas:</span>
                    <span class="text-lg font-bold text-gray-900">
                      {{ formatMoney(Math.max(0, montoRecibido - totalPagos)) }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Alertas/Validaciones -->
              <div v-if="totalPagos > saldoPendiente" class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-yellow-800 flex items-center gap-2">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                  El valor total de los pagos excede el saldo pendiente
                </p>
              </div>

              <!-- Footer con botones -->
              <div class="flex justify-end gap-3">
                <button
                  type="button"
                  @click="handleClose"
                  class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  :disabled="isSaving || totalPagos <= 0"
                  class="px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium disabled:bg-green-300 disabled:cursor-not-allowed flex items-center gap-2"
                >
                  <svg v-if="isSaving" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ isSaving ? 'Procesando...' : 'Registrar Pago' }}
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
import { registrarPagos, fetchFormasPago, getFactura } from '../api/facturacion'
import type { FormaPago } from '../types/factura'

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
const isSaving = ref(false)
const formasPago = ref<FormaPago[]>([])
const facturaInfo = ref<any>(null)
const montoRecibido = ref(0)

// Pagos
interface PagoForm {
  forma_pago_id: number | string
  valor: number
  referencia_externa?: string
  observaciones?: string
}

const pagos = ref<PagoForm[]>([
  { forma_pago_id: '', valor: 0, referencia_externa: '', observaciones: '' }
])

// Computed
const saldoPendiente = computed(() => {
  return facturaInfo.value?.saldo_pendiente || 0
})

const totalPagos = computed(() => {
  return pagos.value.reduce((sum, p) => sum + (p.valor || 0), 0)
})

const nuevoSaldo = computed(() => {
  return Math.max(0, saldoPendiente.value - totalPagos.value)
})

const pagoEnEfectivo = computed(() => {
  return pagos.value.some(p => {
    const forma = formasPago.value.find(f => f.id === Number(p.forma_pago_id))
    return forma?.codigo === 'EFE'
  })
})

// Métodos
const addPago = () => {
  pagos.value.push({ 
    forma_pago_id: '', 
    valor: 0, 
    referencia_externa: '', 
    observaciones: '' 
  })
}

const removePago = (index: number) => {
  pagos.value.splice(index, 1)
}

const needsReference = (formaPagoId: number | string): boolean => {
  if (!formaPagoId) return false
  const forma = formasPago.value.find(f => f.id === Number(formaPagoId))
  return forma?.requiere_referencia || false
}

const formatMoney = (amount: number): string => {
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount)
}

const loadFacturaInfo = async () => {
  if (!props.facturaId) return

  try {
    const info = await getFactura(props.facturaId)
    facturaInfo.value = info

    // Si el saldo es menor al total, sugerir ese monto
    const saldo = info.saldo_pendiente ?? 0
    if (saldo > 0 && pagos.value.length > 0) {
      pagos.value[0].valor = saldo
    }
  } catch (error) {
    console.error('Error loading factura info:', error)
    toast.error('Error al cargar información de la factura')
  }
}


const loadFormasPago = async () => {
  try {
    formasPago.value = await fetchFormasPago()
  } catch (error) {
    console.error('Error loading formas pago:', error)
  }
}

const handleSubmit = async () => {
  // Validaciones
  const pagosValidos = pagos.value.filter(p => p.forma_pago_id && p.valor > 0)
  
  if (pagosValidos.length === 0) {
    toast.warning('Debe agregar al menos un pago válido')
    return
  }
  
  if (totalPagos.value <= 0) {
    toast.warning('El monto total debe ser mayor a 0')
    return
  }
  
  try {
    isSaving.value = true
    
    const payload = {
      pagos: pagosValidos.map(p => ({
        forma_pago_id: Number(p.forma_pago_id),
        valor: p.valor,
        referencia_externa: p.referencia_externa || undefined,
        observaciones: p.observaciones || undefined
      })),
      monto_recibido: montoRecibido.value > 0 ? montoRecibido.value : undefined
    }
    
    const result = await registrarPagos(props.facturaId!, payload)
    
    // Mostrar vueltas si aplica
    if (result.vueltas > 0) {
      toast.success(`Pago registrado. Vueltas: ${formatMoney(result.vueltas)}`, {
        autoClose: 5000
      })
    } else {
      toast.success('Pago registrado exitosamente')
    }
    
    emit('success')
    handleClose()
  } catch (error: any) {
    console.error('Error registrando pago:', error)
    toast.error(error.message || 'Error al registrar el pago')
  } finally {
    isSaving.value = false
  }
}

const handleClose = () => {
  // Reset form
  pagos.value = [{ forma_pago_id: '', valor: 0, referencia_externa: '', observaciones: '' }]
  montoRecibido.value = 0
  facturaInfo.value = null
  emit('close')
}

// Watchers
watch(() => props.open, async (isOpen) => {
  if (isOpen && props.facturaId) {
    await Promise.all([
      loadFacturaInfo(),
      loadFormasPago()
    ])
  }
})
</script>