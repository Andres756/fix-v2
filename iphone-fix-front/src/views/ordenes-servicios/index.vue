<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-800">Sistema de Órdenes de Servicio</h1>
      <div class="flex gap-3">
        <button
          @click="showClientModal = true"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          Crear Cliente
        </button>
        <button
          @click="showOrderModal = true"
          class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Nueva Orden
        </button>
      </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
      <div class="flex gap-4 items-center">
        <div class="flex-1">
          <input
            v-model="searchTerm"
            type="text"
            placeholder="Buscar por cliente o número de orden..."
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />
        </div>
        <select
          v-model="filterStatus"
          class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        >
          <option value="">Todos los estados</option>
          <option value="pendiente">Pendiente</option>
          <option value="recibida">Recibida</option>
          <option value="en_proceso">En proceso</option>
          <option value="finalizada">Finalizada</option>
          <option value="cerrada">Cerrada</option>
        </select>

        <button
          @click="loadOrders"
          :disabled="isLoading"
          class="px-3 py-2 border rounded-lg bg-white hover:bg-gray-50 disabled:opacity-60"
          title="Actualizar"
        >
          {{ isLoading ? 'Cargando…' : 'Actualizar' }}
        </button>
      </div>
    </div>

    <!-- Tabla -->
    <OrdersTable
      :orders="orders"
      :isLoading="isLoading"
      @view="onView"
      @history="onHistory"
      @equipos="onEquipos"
      @create-order="() => (showOrderModal = true)"
    />

    <!-- Modales -->
    <ClienteModal
      :open="showClientModal"
      @close="showClientModal = false"
      @created="onClientCreated"
    />
    <OrdenModal
      :open="showOrderModal"
      @close="showOrderModal = false"
      @created="onOrderCreated"
    />

    <!-- Modal de Equipos -->
    <EquipoModal
      :open="showEquiposModal"
      :clienteId="currentClienteId"
      :ordenId="currentOrdenId"
      @close="showEquiposModal = false"
      @created="loadOrders"
      @updated="loadOrders"
      @deleted="loadOrders"
    />

    <VerOrdenModal
      :open="showViewModal"
      :clienteId="currentClienteId"
      :ordenId="currentOrdenId"
      @close="showViewModal = false"
      @updated="loadOrders"
    />

    <HistorialModal
      :open="showHistorialModal"
      :clienteId="currentClienteId"
      :ordenId="currentOrdenId"
      @close="showHistorialModal = false"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, onBeforeUnmount } from 'vue'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

import ClienteModal from '../../features/OrdenServicio/components/ClienteModal.vue'
import OrdenModal from '../../features/OrdenServicio/components/OrdenModal.vue'
import OrdersTable from '../../features/OrdenServicio/components/OrdersTable.vue'
import EquipoModal from '../../features/OrdenServicio/components/EquipoModal.vue'
import VerOrdenModal from '../../features/OrdenServicio/components/VerOrdenModal.vue'
import HistorialModal from '../../features/OrdenServicio/components/HistorialModal.vue'

import { fetchOrdenesGlobal } from '../../features/OrdenServicio/api/orden'
import type { OrdenServicio } from '../../features/OrdenServicio/types/orden'

// ---------- Estado UI
const showClientModal = ref(false)
const showOrderModal = ref(false)
const showEquiposModal = ref(false)
const isLoading = ref(false)
const showViewModal = ref(false)
const showHistorialModal = ref(false)

// ---------- Contexto para Equipos
const currentClienteId = ref<number>(0)
const currentOrdenId = ref<number>(0)

// ---------- Datos
type OrdenWithCliente = OrdenServicio & {
  cliente?: { id: number; nombre: string; documento: string }
}
const orders = ref<OrdenWithCliente[]>([])

// ---------- Filtros
const searchTerm = ref('')
const filterStatus = ref('')

// ---------- Fetch server-side
async function loadOrders() {
  try {
    isLoading.value = true
    const resp = await fetchOrdenesGlobal({
      q: searchTerm.value || undefined,
      estado: filterStatus.value || undefined,
    })
    orders.value = Array.isArray(resp) ? resp : resp.data
  } catch {
    toast.error('No se pudieron cargar las órdenes.')
  } finally {
    isLoading.value = false
  }
}

// Debounce para filtros
let debounceId: number | undefined
watch([searchTerm, filterStatus], () => {
  if (debounceId) clearTimeout(debounceId)
  debounceId = window.setTimeout(() => {
    loadOrders()
  }, 400)
})
onBeforeUnmount(() => {
  if (debounceId) clearTimeout(debounceId)
})


// ---------- Handlers UI/Modales
function onClientCreated() {
  showClientModal.value = false
  toast.success('Cliente creado.')
}

async function onOrderCreated() {
  showOrderModal.value = false
  toast.success('Orden creada.')
  await loadOrders()
}

function onView(orderId: number, clienteId: number) {
  currentOrdenId.value = orderId
  currentClienteId.value = clienteId
  showViewModal.value = true
}

function onHistory(orderId: number, clienteId: number) {
  currentOrdenId.value = orderId
  currentClienteId.value = clienteId
  showHistorialModal.value = true
}

function onEquipos(orderId: number, clienteId: number) {
  currentOrdenId.value = orderId
  currentClienteId.value = clienteId
  showEquiposModal.value = true
}

// ---------- Mount
onMounted(loadOrders)
</script>
