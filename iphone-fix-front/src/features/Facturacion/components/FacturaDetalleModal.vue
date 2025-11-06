<template>
  <Teleport to="body">
    <transition name="fade">
      <div v-if="open" class="fixed inset-0 z-[9999] bg-black/60 flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden">
          <!-- Header -->
          <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">Detalle de Factura</h2>
            <button
              @click="emit('close')"
              class="p-2 text-gray-500 hover:bg-gray-100 rounded-full"
              title="Cerrar"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Loading -->
          <div v-if="loading" class="p-10 text-center text-gray-500">
            <div class="w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mx-auto mb-3"></div>
            Cargando información de la factura...
          </div>

          <!-- Contenido -->
          <div v-else-if="factura" class="p-6 space-y-6">
            <!-- Info General -->
            <div class="bg-gray-50 rounded-lg p-4 grid grid-cols-2 gap-4 text-sm">
              <div>
                <p class="text-gray-500">Código</p>
                <p class="font-semibold">{{ factura.codigo }}</p>
              </div>
              <div>
                <p class="text-gray-500">Cliente</p>
                <p class="font-semibold">{{ factura.cliente?.nombre }}</p>
              </div>
              <div>
                <p class="text-gray-500">Estado</p>
                <p class="font-semibold">{{ factura.estado?.nombre }}</p>
              </div>
              <div>
                <p class="text-gray-500">Total</p>
                <p class="font-semibold">{{ formatMoney(factura.total) }}</p>
              </div>
              <div>
                <p class="text-gray-500">Fecha emisión</p>
                <p class="font-semibold">{{ formatDate(factura.fecha_emision) }}</p>
              </div>
              <div>
                <p class="text-gray-500">Saldo pendiente</p>
                <p class="font-semibold text-orange-600">
                  {{ formatMoney(factura.saldo_pendiente) }}
                </p>
              </div>
            </div>

            <!-- Tabla de Pagos -->
            <div>
              <h3 class="text-md font-semibold text-gray-900 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Pagos Registrados
              </h3>

              <div v-if="pagos.length" class="border border-gray-200 rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                  <thead class="bg-gray-100 text-gray-600">
                    <tr>
                      <th class="text-left px-4 py-2">Fecha</th>
                      <th class="text-left px-4 py-2">Forma de pago</th>
                      <th class="text-right px-4 py-2">Monto</th>
                      <th class="text-center px-4 py-2">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="p in pagos" :key="p.id" class="border-t border-gray-100">
                      <td class="px-4 py-2">{{ formatDate(p.fecha_pago) }}</td>
                      <td class="px-4 py-2">{{ p.forma_pago?.nombre || '—' }}</td>
                      <td class="px-4 py-2 text-right">{{ formatMoney(p.monto) }}</td>
                      <td class="px-4 py-2 text-center">
                        <button
                          @click="anularPago(p.id)"
                          class="text-red-600 hover:text-red-800 text-xs font-medium"
                          v-if="!p.anulado"
                        >
                          Anular
                        </button>
                        <span v-else class="text-gray-400 text-xs">Anulado</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div v-else class="text-center text-sm text-gray-500 py-6">
                No hay pagos registrados
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { toast } from 'vue3-toastify'
import { getFactura, fetchPagosFactura } from '../api/facturacion'

const props = defineProps<{
  open: boolean
  facturaId: number | null
}>()

const emit = defineEmits<{ (e: 'close'): void }>()

const factura = ref<any>(null)
const pagos = ref<any[]>([])
const loading = ref(false)

const formatMoney = (val: number) =>
  new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(val || 0)

const formatDate = (dateStr: string) =>
  dateStr ? new Date(dateStr).toLocaleDateString('es-CO') : '—'

watch(() => props.open, async (isOpen) => {
  if (isOpen && props.facturaId) {
    await cargarFactura(props.facturaId)
  }
})

async function cargarFactura(id: number) {
  try {
    loading.value = true
    const data = await getFactura(id)
    factura.value = data
    const pagosData = await fetchPagosFactura(id)
    pagos.value = pagosData.pagos || []
  } catch (e: any) {
    toast.error('Error cargando detalles de la factura')
    console.error(e)
  } finally {
    loading.value = false
  }
}

// 🧾 Anular pago (futura acción, backend pendiente)
async function anularPago(id: number) {
  toast.info(`Función de anular pago pendiente (ID: ${id})`)
}
</script>
