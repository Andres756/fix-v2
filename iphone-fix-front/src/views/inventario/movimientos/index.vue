<template>
  <div class="p-6 space-y-6">
    <!-- Header SIN position sticky -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <div class="p-3 bg-purple-100 rounded-lg">
            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Movimientos de Inventario</h1>
            <p class="text-sm text-gray-500 mt-1">Gestiona entradas, salidas y movimientos de stock</p>
          </div>
        </div>

        <button
          @click="abrirExportModal"
          class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
          </svg>
          Exportar
        </button>
      </div>

      <!-- Tabs -->
      <div class="mt-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              'group inline-flex items-center gap-2 py-4 px-1 border-b-2 font-medium text-sm transition-colors',
              activeTab === tab.id
                ? 'border-purple-500 text-purple-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            <component :is="tab.icon" />
            {{ tab.label }}
          </button>
        </nav>
      </div>
    </div>

    <!-- Contenido de Tabs -->
    <div>
      <!-- Tab: Entradas -->
      <EntradasTable
        v-if="activeTab === 'entradas'"
        ref="entradasTableRef"
        @nueva-entrada="modalNuevaEntrada = true"
        @asignar-lote="handleAsignarLote"
      />

      <!-- Tab: Salidas -->
      <SalidasTable v-if="activeTab === 'salidas'" />

      <!-- Tab: Lotes -->
      <LotesTable
        v-if="activeTab === 'lotes'"
        @nuevo-lote="modalNuevoLote = true"
      />

      <!-- Tab: Kardex -->
      <KardexView v-if="activeTab === 'kardex'" />
    </div>

    <!-- Modal: Nueva Entrada -->
    <InventoryEntryModal
      :is-open="modalNuevaEntrada"
      @close="modalNuevaEntrada = false"
      @success="handleEntradaCreada"
    />

    <!-- Modal: Asignar Lote -->
    <AsignarLoteModal
      :is-open="modalAsignarLote.isOpen"
      :entrada="modalAsignarLote.entrada"
      @close="cerrarModalAsignarLote"
      @success="handleLoteAsignado"
    />

    <!-- Modal: Crear Lote -->
    <CrearLoteModal
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

// Ref de la tabla de entradas
const entradasTableRef = ref<any>(null)

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
  }
]

// Métodos
const abrirExportModal = () => {
  toast.info('Función de exportación próximamente')
}

const handleAsignarLote = (entrada: EntradaInventario) => {
  modalAsignarLote.value = {
    isOpen: true,
    entrada
  }
}

const cerrarModalAsignarLote = () => {
  modalAsignarLote.value = {
    isOpen: false,
    entrada: null
  }
}

const handleLoteAsignado = () => {
  toast.success('Lote asignado correctamente')
  cerrarModalAsignarLote()
  // Recargar tabla de entradas
  if (entradasTableRef.value?.recargar) {
    entradasTableRef.value.recargar()
  }
}

const handleEntradaCreada = () => {
  toast.success('Entrada creada correctamente')
  modalNuevaEntrada.value = false
  // Recargar tabla de entradas
  if (entradasTableRef.value?.recargar) {
    entradasTableRef.value.recargar()
  }
}

const handleLoteCreado = () => {
  toast.success('Lote creado correctamente')
  modalNuevoLote.value = false
}
</script>