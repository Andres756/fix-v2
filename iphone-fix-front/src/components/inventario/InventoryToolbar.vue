<template>
  <div class="bg-white rounded-lg shadow p-4">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
      <!-- Barra de búsqueda -->
      <div class="flex-1 max-w-md">
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <input
            v-model="localSearchQuery"
            @keyup.enter="buscar"
            type="text"
            placeholder="Buscar por nombre o código..."
            class="block w-full pl-10 pr-12 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            :disabled="loading"
          />
          <div class="absolute inset-y-0 right-0 flex items-center">
            <button
              v-if="localSearchQuery"
              @click="limpiarBusqueda"
              class="p-2 text-gray-400 hover:text-gray-500"
              type="button"
            >
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
            <button
              @click="buscar"
              :disabled="loading"
              class="p-2 text-indigo-600 hover:text-indigo-500 disabled:opacity-50"
              type="button"
            >
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <!-- Toggle de vista -->
        <div class="inline-flex rounded-lg border border-gray-200 p-1 bg-gray-50">
          <button
            @click="cambiarVista('table')"
            :class="[
              'inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium transition-colors',
              viewMode === 'table'
                ? 'bg-white text-gray-900 shadow-sm'
                : 'text-gray-500 hover:text-gray-700'
            ]"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 6h18m-9 8h9" />
            </svg>
            Tabla
          </button>
          <button
            @click="cambiarVista('cards')"
            :class="[
              'inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium transition-colors',
              viewMode === 'cards'
                ? 'bg-white text-gray-900 shadow-sm'
                : 'text-gray-500 hover:text-gray-700'
            ]"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-7H5a2 2 0 00-2 2v12a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2z" />
            </svg>
            Tarjetas
          </button>
        </div>

        <!-- Ordenación -->
        <div class="flex items-center space-x-2">
          <label class="text-sm font-medium text-gray-700 whitespace-nowrap">
            Ordenar por:
          </label>
          <select
            v-model="localSortBy"
            @change="cambiarOrdenacion"
            class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
            :disabled="loading"
          >
            <option value="nombre">Nombre</option>
            <option value="codigo">Código</option>
            <option value="precio_final">Precio</option>
            <option value="stock">Stock</option>
            <option value="fecha_ingreso">Fecha Ingreso</option>
          </select>
          <button
            @click="toggleSortOrder"
            :disabled="loading"
            class="p-2 text-gray-400 hover:text-gray-500 disabled:opacity-50"
          >
            <svg 
              :class="['w-4 h-4 transform transition-transform', { 'rotate-180': sortOrder === 'desc' }]" 
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
            </svg>
          </button>
        </div>

        <!-- Items por página -->
        <div class="flex items-center space-x-2">
          <label class="text-sm font-medium text-gray-700 whitespace-nowrap">
            Mostrar:
          </label>
          <select
            v-model="localPerPage"
            @change="cambiarPerPage"
            class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
            :disabled="loading"
          >
            <option :value="10">10</option>
            <option :value="25">25</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Acciones masivas (mostrar solo si hay items seleccionados) -->
    <div v-if="selectedItems.size > 0" class="mt-4 pt-4 border-t border-gray-200">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <span class="text-sm font-medium text-gray-700">
            {{ selectedItems.size }} elemento{{ selectedItems.size !== 1 ? 's' : '' }} seleccionado{{ selectedItems.size !== 1 ? 's' : '' }}
          </span>
          <button
            @click="limpiarSeleccion"
            class="text-sm text-indigo-600 hover:text-indigo-500"
          >
            Limpiar selección
          </button>
        </div>
        
        <div class="flex items-center space-x-2">
          <button
            @click="accionMasiva('activate')"
            class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Activar
          </button>
          
          <button
            @click="accionMasiva('deactivate')"
            class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Desactivar
          </button>

          <button
            @click="accionMasiva('export')"
            class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Exportar
          </button>

          <button
            @click="accionMasiva('delete')"
            class="inline-flex items-center px-3 py-1.5 border border-red-300 shadow-sm text-xs font-medium rounded text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Eliminar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'

// Props
interface Props {
  searchQuery: string
  viewMode: 'table' | 'cards'
  sortBy: string
  sortOrder: 'asc' | 'desc'
  perPage: number
  selectedItems: Set<number>
  loading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  loading: false
})

// Emits
const emit = defineEmits<{
  'update:searchQuery': [query: string]
  'update:viewMode': [mode: 'table' | 'cards']
  'update:sortBy': [sortBy: string]
  'update:sortOrder': [order: 'asc' | 'desc']
  'update:perPage': [perPage: number]
  search: []
  'clear-search': []
  'bulk-action': [action: string]
}>()

// Estado local
const localSearchQuery = ref(props.searchQuery)
const localSortBy = ref(props.sortBy)
const localPerPage = ref(props.perPage)

// Métodos
const buscar = () => {
  emit('update:searchQuery', localSearchQuery.value)
  emit('search')
}

const limpiarBusqueda = () => {
  localSearchQuery.value = ''
  emit('update:searchQuery', '')
  emit('clear-search')
}

const cambiarVista = (mode: 'table' | 'cards') => {
  emit('update:viewMode', mode)
}

const cambiarOrdenacion = () => {
  emit('update:sortBy', localSortBy.value)
}

const toggleSortOrder = () => {
  const newOrder = props.sortOrder === 'asc' ? 'desc' : 'asc'
  emit('update:sortOrder', newOrder)
}

const cambiarPerPage = () => {
  emit('update:perPage', localPerPage.value)
}

const limpiarSeleccion = () => {
  // Esta acción será manejada por el componente padre
  emit('bulk-action', 'clear-selection')
}

const accionMasiva = (action: string) => {
  emit('bulk-action', action)
}

// Watchers
watch(() => props.searchQuery, (newQuery) => {
  localSearchQuery.value = newQuery
})

watch(() => props.sortBy, (newSortBy) => {
  localSortBy.value = newSortBy
})

watch(() => props.perPage, (newPerPage) => {
  localPerPage.value = newPerPage
})
</script>