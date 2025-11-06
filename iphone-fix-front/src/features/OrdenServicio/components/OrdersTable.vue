<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <!-- Loading state mejorado -->
    <div v-if="isLoading" class="p-12 text-center">
      <div class="inline-flex items-center gap-3 text-gray-500">
        <svg class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        <span class="text-sm font-medium">Cargando órdenes...</span>
      </div>
    </div>

    <!-- Tabla con datos -->
    <div v-else class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
              Orden
            </th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
              Cliente
            </th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
              Estado
            </th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
              Fecha de Entrega
            </th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
              Acciones
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50 transition-colors">
            <!-- Número de orden -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-3">
                <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                <span class="text-sm font-mono font-medium text-gray-900">
                  OS-{{ String(order.id).padStart(4, '0') }}
                </span>
              </div>
            </td>

            <!-- Cliente -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                  <span class="text-xs font-semibold text-blue-700">
                    {{ (order.cliente?.nombre || 'N').charAt(0).toUpperCase() }}
                  </span>
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-900">
                    {{ order.cliente?.nombre || 'Cliente no asignado' }}
                  </div>
                  <div class="text-xs text-gray-500 font-mono">
                    {{ order.cliente?.documento || `ID: ${order.cliente_id}` }}
                  </div>
                </div>
              </div>
            </td>

            <!-- Estado -->
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="getStatusClass(order.estado)" class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full">
                <div :class="getStatusDotClass(order.estado)" class="w-1.5 h-1.5 rounded-full"></div>
                {{ getStatusLabel(order.estado) }}
              </span>
            </td>

            <!-- Fecha de entrega -->
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ formatDate(order.fecha_cierre) }}
              </div>
            </td>

            <!-- Acciones -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-2">
                <button
                  @click="$emit('view', order.id, order.cliente_id || order.cliente?.id)"
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  Ver
                </button>

                <button
                  @click="$emit('history', order.id, order.cliente_id)"
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Historial
                </button>

                <!-- NUEVO: Botón Equipos -->
                <button
                  @click="$emit('equipos', order.id, order.cliente_id)"
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-violet-700 bg-violet-100 rounded-lg hover:bg-violet-200 transition-colors"
                  title="Gestionar equipos de esta orden"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3a1.5 1.5 0 00-1.5 1.5V7.5m7.5-4.5A1.5 1.5 0 0117.25 3V7.5M4.5 9.75h15m-12 0v7.5a1.5 1.5 0 001.5 1.5h6a1.5 1.5 0 001.5-1.5v-7.5" />
                  </svg>
                  Equipos
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Empty state mejorado -->
    <div v-if="!isLoading && orders.length === 0" class="text-center py-16">
      <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">No hay órdenes de servicio</h3>
      <p class="text-gray-500 mb-6 max-w-sm mx-auto">
        Aún no se han creado órdenes de servicio. Comienza creando tu primera orden.
      </p>
      <button
        @click="$emit('create-order')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Crear Primera Orden
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { OrdenServicio } from '../types/orden'

type OrdenWithCliente = OrdenServicio & {
  cliente?: { id: number; nombre: string; documento: string }
}

const props = defineProps<{
  orders: OrdenWithCliente[]
  isLoading?: boolean
}>()

defineEmits<{
  (e: 'view', orderId: number): void
  (e: 'history', orderId: number, clienteId: number): void
  (e: 'equipos', orderId: number, clienteId: number): void
  (e: 'create-order'): void
}>()

const getStatusClass = (status: string) => {
  const classes: Record<string, string> = {
    pendiente: 'bg-yellow-50 text-yellow-700 border border-yellow-200',
    recibida: 'bg-blue-50 text-blue-700 border border-blue-200',
    en_proceso: 'bg-orange-50 text-orange-700 border border-orange-200',
    finalizada: 'bg-green-50 text-green-700 border border-green-200',
    cerrada: 'bg-gray-50 text-gray-700 border border-gray-200'
  }
  return classes[status] || 'bg-gray-50 text-gray-700 border border-gray-200'
}

const getStatusDotClass = (status: string) => {
  const classes: Record<string, string> = {
    pendiente: 'bg-yellow-400',
    recibida: 'bg-blue-400',
    en_proceso: 'bg-orange-400',
    finalizada: 'bg-green-400',
    cerrada: 'bg-gray-400'
  }
  return classes[status] || 'bg-gray-400'
}

const getStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    pendiente: 'Pendiente',
    recibida: 'Recibida',
    en_proceso: 'En Proceso',
    finalizada: 'Finalizada',
    cerrada: 'Cerrada'
  }
  return labels[status] || status
}

const formatDate = (date?: string | null) => {
  if (!date) return 'No definida'
  return new Date(date).toLocaleDateString('es-CO', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}
</script>
