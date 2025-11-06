<!-- features/Facturacion/components/FacturasTable.vue -->
<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <!-- Contenedor con scroll horizontal para móvil -->
    <div class="overflow-x-auto">
      <table class="w-full">
        <!-- Header -->
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
              Código
            </th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
              Cliente
            </th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
              Tipo
            </th>
            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
              Estado
            </th>
            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
              Total
            </th>
            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
              Saldo
            </th>
            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
              Fecha
            </th>
            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
              Acciones
            </th>
          </tr>
        </thead>

        <!-- Body -->
        <tbody class="divide-y divide-gray-200">
          <!-- Loading State -->
          <tr v-if="isLoading">
            <td colspan="8" class="px-6 py-12">
              <div class="flex flex-col items-center justify-center space-y-4">
                <div class="w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                <p class="text-sm text-gray-500">Cargando facturas...</p>
              </div>
            </td>
          </tr>

          <!-- Empty State -->
          <tr v-else-if="!facturas.length">
            <td colspan="8" class="px-6 py-12">
              <div class="flex flex-col items-center justify-center space-y-4">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                  <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
                <div class="text-center">
                  <p class="text-gray-900 font-medium">No hay facturas</p>
                  <p class="text-sm text-gray-500 mt-1">Comienza creando tu primera factura</p>
                </div>
                <button
                  @click="$emit('create-factura')"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                  Nueva Factura
                </button>
              </div>
            </td>
          </tr>

          <!-- Data Rows -->
          <template v-else>
            <tr v-for="factura in facturas" :key="factura.id" 
                class="hover:bg-gray-50 transition-colors">
              <!-- Código -->
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <span class="font-medium text-gray-900">{{ factura.codigo }}</span>
                  <span v-if="factura.es_prefactura" 
                        class="px-2 py-0.5 text-xs font-medium bg-purple-100 text-purple-700 rounded">
                    PRE
                  </span>
                </div>
              </td>

              <!-- Cliente -->
              <td class="px-6 py-4">
                <div>
                  <p class="font-medium text-gray-900">{{ factura.cliente?.nombre || '—' }}</p>
                  <p class="text-sm text-gray-500">{{ factura.cliente?.documento || '—' }}</p>
                </div>
              </td>

              <!-- Tipo -->
              <td class="px-6 py-4">
                <span :class="getTipoClass(factura.tipo_venta?.codigo)">
                  {{ factura.tipo_venta?.nombre || '—' }}
                </span>
              </td>

              <!-- Estado -->
              <td class="px-6 py-4 text-center">
                <span :class="getEstadoClass(factura.estado?.codigo)">
                  {{ factura.estado?.nombre || '—' }}
                </span>
              </td>

              <!-- Total -->
              <td class="px-6 py-4 text-right">
                <p class="font-semibold text-gray-900">
                  {{ formatMoney(factura.total) }}
                </p>
              </td>

              <!-- Saldo -->
              <td class="px-6 py-4 text-right">
                <p v-if="factura.saldo_pendiente && factura.saldo_pendiente > 0"
                   class="font-medium text-orange-600">
                  {{ formatMoney(factura.saldo_pendiente) }}
                </p>
                <p v-else class="text-sm text-gray-500">—</p>
              </td>

              <!-- Fecha -->
              <td class="px-6 py-4 text-center">
                <p class="text-sm text-gray-900">
                  {{ formatDate(factura.fecha_emision) }}
                </p>
                <p class="text-xs text-gray-500">
                  {{ formatTime(factura.fecha_emision) }}
                </p>
              </td>

              <!-- Acciones -->
              <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-1">
                  <!-- Ver detalle -->
                  <button
                    @click="$emit('view', factura.id)"
                    class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                    title="Ver detalle"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>

                  <!-- Imprimir -->
                  <button
                    @click="verFactura(factura.id)"
                    class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                    title="Imprimir factura"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                  </button>

                  <!-- Registrar Pago (solo si hay saldo pendiente) -->
                  <button
                    v-if="factura.estado?.codigo !== 'ANUL' && factura.estado?.codigo !== 'PAGA'"
                    @click="$emit('pagar', factura.id)"
                    class="p-2 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                    title="Registrar pago"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                  </button>

                  <!-- Anular (solo si no está anulada) -->
                  <button
                    v-if="factura.estado?.codigo !== 'ANUL'"
                    @click="$emit('anular', factura.id)"
                    class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                    title="Anular factura"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                  </button>

                  <!-- Convertir prefactura -->
                  <button
                    v-if="factura.es_prefactura"
                    @click="$emit('convertir', factura.id)"
                    class="p-2 text-gray-600 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors"
                    title="Convertir a factura"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Factura } from '../types/factura'
import { getFacturaPrintUrl } from '../api/facturacion'
import { toast } from 'vue3-toastify'

// Props
interface Props {
  facturas: Factura[]
  isLoading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  isLoading: false
})

// Emits
const emit = defineEmits<{
  (e: 'view', id: number): void
  (e: 'print', id: number): void
  (e: 'pagar', id: number): void
  (e: 'anular', id: number): void
  (e: 'convertir', id: number): void
  (e: 'create-factura'): void
}>()

// Helpers de formato (mantén los tuyos aquí)
const formatMoney = (amount: number): string => {
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount)
}

const formatDate = (dateStr: string): string => {
  if (!dateStr) return '—'
  const date = new Date(dateStr)
  return date.toLocaleDateString('es-CO', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

const formatTime = (dateStr: string): string => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return date.toLocaleTimeString('es-CO', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Estado visual
const getEstadoClass = (codigo?: string): string => {
  const base = 'px-3 py-1 text-xs font-semibold rounded-full inline-flex items-center'
  switch (codigo) {
    case 'PEND': return `${base} bg-yellow-100 text-yellow-800`
    case 'PARC': return `${base} bg-blue-100 text-blue-800`
    case 'PAGA': return `${base} bg-green-100 text-green-800`
    case 'ANUL': return `${base} bg-red-100 text-red-800`
    default: return `${base} bg-gray-100 text-gray-800`
  }
}

const getTipoClass = (codigo?: string): string => {
  const base = 'px-2 py-1 text-xs font-medium rounded'
  switch (codigo) {
    case 'VD': return `${base} bg-indigo-100 text-indigo-700`
    case 'SRV': return `${base} bg-cyan-100 text-cyan-700`
    case 'PS': return `${base} bg-purple-100 text-purple-700`
    default: return `${base} bg-gray-100 text-gray-700`
  }
}

// ✅ Abrir ticket o PDF directamente
const verFactura = async (facturaId: number) => {
  try {
    const url = await getFacturaPrintUrl(facturaId)
    if (!url) throw new Error('No se pudo obtener la URL del ticket')
    window.open(url, '_blank')
  } catch (error: any) {
    console.error('Error al imprimir la factura:', error)
    toast.error('Error al obtener la factura para imprimir')
  }
}
</script>


