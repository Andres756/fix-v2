<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-700 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Movimientos de Inventario</h1>
              <p class="text-sm text-gray-500">Gestiona entradas, salidas y movimientos de stock</p>
            </div>
          </div>

          <div class="flex items-center gap-3">
            <button
              @click="() => {}"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
            >
              Exportar
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex space-x-8" aria-label="Tabs">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
              activeTab === tab.id
                ? 'border-purple-500 text-purple-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            <div class="flex items-center gap-2">
              <component :is="tab.icon" class="w-5 h-5" />
              {{ tab.label }}
            </div>
          </button>
        </nav>
      </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Tab: Entradas -->
      <div v-if="activeTab === 'entradas'">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
          <EntradasTable 
            @ver-detalle="verDetalleEntrada"
            @asignar-lote="asignarLote"
          />
        </div>
      </div>

      <!-- Tab: Salidas -->
      <div v-else-if="activeTab === 'salidas'">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
          <SalidasTable />
        </div>
      </div>

      <!-- Tab: Lotes -->
      <div v-else-if="activeTab === 'lotes'">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
          <LotesTable />
        </div>
      </div>

      <!-- Tab: Kardex -->
      <div v-else-if="activeTab === 'kardex'">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
          <KardexView />
        </div>
      </div>
    </div>

    <!-- Modales -->
    <InventoryEntryModal
      v-if="modalNuevaEntrada"
      :is-open="modalNuevaEntrada"
      @close="modalNuevaEntrada = false"
      @success="handleEntradaCreada"
    />

    <AsignarLoteModal
      v-if="modalAsignarLote.isOpen"
      :is-open="modalAsignarLote.isOpen"
      :entrada="modalAsignarLote.entrada"
      @close="modalAsignarLote.isOpen = false"
      @success="handleLoteAsignado"
    />

    <CrearLoteModal
      v-if="modalNuevoLote"
      :is-open="modalNuevoLote"
      @close="modalNuevoLote = false"
      @success="handleLoteCreado"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, h } from 'vue'
import { toast } from 'vue3-toastify'

// Componentes del módulo movimientos
import EntradasTable from '../../../features/movimientos-inventario/components/Entradastable.vue'
import SalidasTable from '../../../features/movimientos-inventario/components/SalidasTable.vue'
import LotesTable from '../../../features/movimientos-inventario/components/Lotestable.vue'
import KardexView from '../../../features/movimientos-inventario/components/Kardexview.vue'
import AsignarLoteModal from '../../../features/movimientos-inventario/components/AsignarLoteModal.vue'
import CrearLoteModal from '../../../features/movimientos-inventario/components/CrearLoteModal.vue'

// 🔄 Reutilizamos el modal de entrada del módulo inventario
import InventoryEntryModal from '../../../features/inventario/components/InventoryEntryModal.vue'

import type { EntradaInventario } from '../../../features/inventario/types/inventoryEntry'

// Estado
const activeTab = ref<'entradas' | 'salidas' | 'lotes' | 'kardex'>('entradas')
const modalNuevaEntrada = ref(false)
const modalNuevoLote = ref(false)
const modalAsignarLote = ref<{
  isOpen: boolean
  entrada: EntradaInventario | null
}>({
  isOpen: false,
  entrada: null,
})

// Tabs configuration
const tabs = [
  {
    id: 'entradas',
    label: 'Entradas',
    icon: () => h('svg', {
      class: 'w-5 h-5',
      fill: 'none',
      stroke: 'currentColor',
      viewBox: '0 0 24 24'
    }, [
      h('path', {
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
        'stroke-width': '2',
        d: 'M7 16V4m0 0L3 8m4-4l4 4'
      })
    ])
  },
  {
    id: 'salidas',
    label: 'Salidas',
    icon: () => h('svg', {
      class: 'w-5 h-5',
      fill: 'none',
      stroke: 'currentColor',
      viewBox: '0 0 24 24'
    }, [
      h('path', {
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
        'stroke-width': '2',
        d: 'M17 8l4 4m0 0l-4 4m4-4H3'
      })
    ])
  },
  {
    id: 'lotes',
    label: 'Lotes',
    icon: () => h('svg', {
      class: 'w-5 h-5',
      fill: 'none',
      stroke: 'currentColor',
      viewBox: '0 0 24 24'
    }, [
      h('path', {
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
        'stroke-width': '2',
        d: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'
      })
    ])
  },
  {
    id: 'kardex',
    label: 'Kardex',
    icon: () => h('svg', {
      class: 'w-5 h-5',
      fill: 'none',
      stroke: 'currentColor',
      viewBox: '0 0 24 24'
    }, [
      h('path', {
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
        'stroke-width': '2',
        d: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
      })
    ])
  },
]

// Métodos
const verDetalleEntrada = (entrada: EntradaInventario) => {
  console.log('Ver detalle:', entrada)
}

const asignarLote = (entrada: EntradaInventario) => {
  modalAsignarLote.value = {
    isOpen: true,
    entrada,
  }
}

const handleEntradaCreada = () => {
  modalNuevaEntrada.value = false
  toast.success('Entrada registrada correctamente')
  // Recargar tabla de entradas
}

const handleLoteAsignado = () => {
  modalAsignarLote.value = {
    isOpen: false,
    entrada: null,
  }
  toast.success('Lote asignado correctamente')
  // Recargar tabla de entradas
}

const handleLoteCreado = () => {
  modalNuevoLote.value = false
  toast.success('Lote creado correctamente')
  // Recargar tabla de lotes
}
</script>