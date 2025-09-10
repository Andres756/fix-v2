<template>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <div
      v-for="item in items"
      :key="item.id"
      :class="[
        'bg-white rounded-lg shadow hover:shadow-lg transition-shadow cursor-pointer border',
        { 'border-indigo-500 ring-2 ring-indigo-200': selectedItems.has(item.id) }
      ]"
      @click="verDetalle(item)"
    >
      <!-- Header con checkbox y menú -->
      <div class="flex items-center justify-between p-4 pb-2">
        <input
        type="checkbox"
        :checked="selectedItems.has(item.id)"
        @change="onRowCheckChange(item.id, $event)"
        @click.stop
        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        />
        
        <!-- Menú de acciones -->
        <div class="relative" @click.stop>
          <button
            @click="toggleMenu(item.id)"
            class="p-1 text-gray-400 hover:text-gray-500 rounded-full hover:bg-gray-100"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01" />
            </svg>
          </button>
          
          <!-- Dropdown menu -->
          <div
            v-if="activeMenu === item.id"
            v-click-outside="() => closeMenu()"
            class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10"
          >
            <div class="py-1" role="menu">
              <button
                @click="verDetalle(item)"
                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
              >
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Ver detalle
              </button>
              <button
                @click="editarItem(item)"
                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
              >
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Editar
              </button>
              <button
                @click="verMovimientos(item)"
                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
              >
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Ver movimientos
              </button>
              <hr class="my-1" />
              <button
                @click="eliminarItem(item)"
                class="flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50"
                role="menuitem"
              >
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Eliminar
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Imagen del producto -->
      <div class="px-4 pb-2">
        <div class="w-full h-48 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center">
          <img
            v-if="item.ruta_imagen"
            :src="item.ruta_imagen"
            :alt="item.nombre"
            class="w-full h-full object-cover"
            @error="handleImageError"
          />
          <svg
            v-else
            class="w-16 h-16 text-gray-400"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </div>
      </div>

      <!-- Contenido de la tarjeta -->
      <div class="px-4 pb-4">
        <!-- Nombre y código -->
        <div class="mb-3">
          <h3 class="text-lg font-semibold text-gray-900 truncate" :title="item.nombre">
            {{ item.nombre }}
          </h3>
          <p class="text-sm text-gray-500">
            {{ item.codigo }}
          </p>
        </div>

        <!-- Tipo y categoría -->
        <div class="flex flex-wrap gap-2 mb-3">
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
            {{ item.tipo_inventario_nombre }}
          </span>
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
            {{ item.categoria_nombre }}
          </span>
        </div>

        <!-- Estado -->
        <div class="mb-3">
          <span :class="[
            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
            getEstadoBadgeClass(item.estado_inventario_nombre)
          ]">
            <div
              v-if="item.stock <= item.stock_minimo"
              class="w-2 h-2 bg-current rounded-full mr-1.5"
            ></div>
            {{ item.estado_inventario_nombre }}
          </span>
        </div>

        <!-- Precio destacado -->
        <div class="mb-4">
          <div class="text-2xl font-bold text-gray-900">
            ${{ formatCurrency(item.precio_final) }}
          </div>
          <div class="text-sm text-gray-500">
            Base: ${{ formatCurrency(item.precio) }}
            <span class="text-xs">(+ {{ item.tipo_impuesto }} {{ (item.valor_impuesto * 100).toFixed(0) }}%)</span>
          </div>
        </div>

        <!-- Stock con indicador visual -->
        <div class="mb-3">
          <div class="flex items-center justify-between mb-1">
            <span class="text-sm font-medium text-gray-700">Stock</span>
            <span :class="[
              'text-sm font-semibold',
              getStockColorClass(item.stock, item.stock_minimo)
            ]">
              {{ item.stock }} / {{ item.stock_minimo }} mín
            </span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div
              :class="[
                'h-2 rounded-full transition-all',
                getStockBarColorClass(item.stock, item.stock_minimo)
              ]"
              :style="{ width: getStockPercentage(item.stock, item.stock_minimo) + '%' }"
            ></div>
          </div>
        </div>

        <!-- Información adicional -->
        <div class="text-xs text-gray-500 space-y-1">
          <div v-if="item.proveedor_nombre" class="flex items-center">
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            {{ item.proveedor_nombre }}
          </div>
          <div v-if="item.lote_codigo" class="flex items-center">
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            Lote: {{ item.lote_codigo }}
          </div>
          <div class="flex items-center">
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0l4 4m2-4l4 4m-4-4v10a2 2 0 01-2 2H8a2 2 0 01-2-2V7a2 2 0 012-2z" />
            </svg>
            {{ formatDate(item.fecha_ingreso) }}
          </div>
        </div>
      </div>

      <!-- Footer con indicador de estado activo/inactivo -->
      <div v-if="!item.activo" class="px-4 py-2 bg-gray-50 rounded-b-lg border-t">
        <div class="flex items-center">
          <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636" />
          </svg>
          <span class="text-sm text-red-700 font-medium">Producto inactivo</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

interface InventarioItem {
  id: number
  ruta_imagen: string | null
  nombre: string
  codigo: string
  tipo_inventario_nombre: string
  categoria_nombre: string
  estado_inventario_nombre: string
  proveedor_nombre: string | null
  lote_codigo: string | null
  stock: number
  stock_minimo: number
  precio: number
  tipo_impuesto: string
  valor_impuesto: number
  precio_final: number
  activo: boolean
  fecha_ingreso: string
}

// Props
interface Props {
  items: InventarioItem[]
  selectedItems: Set<number>
}

const onRowCheckChange = (id: number, e: Event) => {
  const checked = (e.target as HTMLInputElement).checked
  toggleSelect(id, checked)
}


const props = defineProps<Props>()

// Emits
const emit = defineEmits<{
  select: [id: number, selected: boolean]
  view: [item: InventarioItem]
  edit: [item: InventarioItem]
  movements: [item: InventarioItem]
  delete: [item: InventarioItem]
}>()

// Estado local
const activeMenu = ref<number | null>(null)

import type { ObjectDirective } from 'vue'

type ClickOutsideEl = HTMLElement & {
  _clickOutsideHandler?: (e: MouseEvent) => void
}

const vClickOutside: ObjectDirective<ClickOutsideEl, () => void> = {
  mounted(el, binding) {
    const handler = (e: MouseEvent) => {
      const t = e.target
      if (!(t instanceof Node)) return
      if (el !== t && !el.contains(t)) binding.value?.()
    }
    el._clickOutsideHandler = handler
    document.addEventListener('click', handler)
  },
  unmounted(el) {
    if (el._clickOutsideHandler) {
      document.removeEventListener('click', el._clickOutsideHandler)
      delete el._clickOutsideHandler
    }
  },
}


// Métodos
const toggleSelect = (id: number, selected: boolean) => {
  emit('select', id, selected)
}

const toggleMenu = (id: number) => {
  activeMenu.value = activeMenu.value === id ? null : id
}

const closeMenu = () => {
  activeMenu.value = null
}

const verDetalle = (item: InventarioItem) => {
  closeMenu()
  emit('view', item)
}

const editarItem = (item: InventarioItem) => {
  closeMenu()
  emit('edit', item)
}

const verMovimientos = (item: InventarioItem) => {
  closeMenu()
  emit('movements', item)
}

const eliminarItem = (item: InventarioItem) => {
  closeMenu()
  emit('delete', item)
}

const handleImageError = (event: Event) => {
  const target = event.target as HTMLImageElement
  target.style.display = 'none'
}

// Utilidades de formato
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('es-CO').format(amount)
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('es-CO', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Utilidades de estilo
const getStockColorClass = (stock: number, stockMinimo: number) => {
  if (stock === 0) return 'text-red-600'
  if (stock <= stockMinimo) return 'text-orange-600'
  return 'text-green-600'
}

const getStockBarColorClass = (stock: number, stockMinimo: number) => {
  if (stock === 0) return 'bg-red-500'
  if (stock <= stockMinimo) return 'bg-orange-500'
  return 'bg-green-500'
}

const getStockPercentage = (stock: number, stockMinimo: number) => {
  if (stock === 0) return 0
  const maxStock = Math.max(stockMinimo * 3, stock)
  return Math.min((stock / maxStock) * 100, 100)
}

const getEstadoBadgeClass = (estado: string) => {
  const estadoLower = estado.toLowerCase()
  
  if (estadoLower.includes('stock') || estadoLower.includes('disponible')) {
    return 'bg-green-100 text-green-800'
  } else if (estadoLower.includes('sin stock') || estadoLower.includes('agotado')) {
    return 'bg-red-100 text-red-800'
  } else if (estadoLower.includes('reservado') || estadoLower.includes('pendiente')) {
    return 'bg-yellow-100 text-yellow-800'
  } else if (estadoLower.includes('dañado') || estadoLower.includes('defectuoso')) {
    return 'bg-red-100 text-red-800'
  } else if (estadoLower.includes('tránsito')) {
    return 'bg-blue-100 text-blue-800'
  }
  
  return 'bg-gray-100 text-gray-800'
}
</script>