<template>
  <div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <!-- Checkbox para seleccionar todos -->
            <th class="px-6 py-3 text-left">
              <input
                type="checkbox"
                :checked="isAllSelected"
                :indeterminate="isPartiallySelected"
                @change="toggleSelectAll"
                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              />
            </th>
            
            <!-- Imagen -->
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Imagen
            </th>
            
            <!-- Nombre/Código -->
            <th 
              @click="ordenar('nombre')"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 select-none"
            >
              <div class="flex items-center space-x-1">
                <span>Producto</span>
                <svg 
                  v-if="sortBy === 'nombre'" 
                  :class="['w-4 h-4 transform transition-transform', { 'rotate-180': sortOrder === 'desc' }]" 
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                </svg>
                <svg 
                  v-else 
                  class="w-4 h-4 text-gray-300" 
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                </svg>
              </div>
            </th>
            
            <!-- Tipo/Categoría -->
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Tipo/Categoría
            </th>
            
            <!-- Stock -->
            <th 
              @click="ordenar('stock')"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 select-none"
            >
              <div class="flex items-center space-x-1">
                <span>Stock</span>
                <svg 
                  v-if="sortBy === 'stock'" 
                  :class="['w-4 h-4 transform transition-transform', { 'rotate-180': sortOrder === 'desc' }]" 
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                </svg>
                <svg 
                  v-else 
                  class="w-4 h-4 text-gray-300" 
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                </svg>
              </div>
            </th>
            
            <!-- Estado -->
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Estado
            </th>
            
            <!-- Precio -->
            <th 
              @click="ordenar('precio_final')"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 select-none"
            >
              <div class="flex items-center space-x-1">
                <span>Precio</span>
                <svg 
                  v-if="sortBy === 'precio_final'" 
                  :class="['w-4 h-4 transform transition-transform', { 'rotate-180': sortOrder === 'desc' }]" 
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                </svg>
                <svg 
                  v-else 
                  class="w-4 h-4 text-gray-300" 
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                </svg>
              </div>
            </th>
            
            <!-- Proveedor/Lote -->
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Proveedor
            </th>
            
            <!-- Fecha -->
            <th 
              @click="ordenar('fecha_ingreso')"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 select-none"
            >
              <div class="flex items-center space-x-1">
                <span>Fecha Ingreso</span>
                <svg 
                  v-if="sortBy === 'fecha_ingreso'" 
                  :class="['w-4 h-4 transform transition-transform', { 'rotate-180': sortOrder === 'desc' }]" 
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                </svg>
                <svg 
                  v-else 
                  class="w-4 h-4 text-gray-300" 
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                </svg>
              </div>
            </th>
            
            <!-- Acciones -->
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Acciones
            </th>
          </tr>
        </thead>
        
        <tbody class="bg-white divide-y divide-gray-200">
          <tr 
            v-for="item in items" 
            :key="item.id"
            :class="[
              'hover:bg-gray-50 transition-colors cursor-pointer',
              { 'bg-blue-50': selectedItems.has(item.id) }
            ]"
            @click="verDetalle(item)"
          >
            <!-- Checkbox -->
            <td class="px-6 py-4 whitespace-nowrap" @click.stop>
                <input
                type="checkbox"
                :checked="selectedItems.has(item.id)"
                @change="onRowCheckChange(item.id, $event)"
                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
            </td>
            
            <!-- Imagen -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center">
                <img
                  v-if="item.ruta_imagen"
                  :src="item.ruta_imagen"
                  :alt="item.nombre"
                  class="w-full h-full object-cover"
                  @error="handleImageError"
                />
                <svg
                  v-else
                  class="w-8 h-8 text-gray-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
            </td>
            
            <!-- Nombre/Código -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex flex-col">
                <div class="text-sm font-medium text-gray-900">
                  {{ item.nombre }}
                </div>
                <div class="text-sm text-gray-500">
                  {{ item.codigo }}
                </div>
                <div v-if="item.nombre_detallado !== item.nombre" class="text-xs text-gray-400 mt-1">
                  {{ item.nombre_detallado }}
                </div>
              </div>
            </td>
            
            <!-- Tipo/Categoría -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex flex-col space-y-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  {{ item.tipo_inventario_nombre }}
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                  {{ item.categoria_nombre }}
                </span>
              </div>
            </td>
            
            <!-- Stock -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex flex-col items-start">
                <div class="flex items-center space-x-2">
                  <span :class="[
                    'text-sm font-medium',
                    getStockColorClass(item.stock, item.stock_minimo)
                  ]">
                    {{ item.stock }}
                  </span>
                  <div
                    v-if="item.stock <= item.stock_minimo"
                    class="w-2 h-2 bg-red-500 rounded-full"
                    title="Stock bajo"
                  ></div>
                </div>
                <div class="text-xs text-gray-500 mt-1">
                  Mín: {{ item.stock_minimo }}
                </div>
                <!-- Barra de progreso visual -->
                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                  <div
                    :class="[
                      'h-1.5 rounded-full transition-all',
                      getStockBarColorClass(item.stock, item.stock_minimo)
                    ]"
                    :style="{ width: getStockPercentage(item.stock, item.stock_minimo) + '%' }"
                  ></div>
                </div>
              </div>
            </td>
            
            <!-- Estado -->
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="[
                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                getEstadoBadgeClass(item.estado_inventario_nombre)
              ]">
                {{ item.estado_inventario_nombre }}
              </span>
            </td>
            
            <!-- Precio -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex flex-col">
                <div class="text-sm font-medium text-gray-900">
                  ${{ formatCurrency(item.precio_final) }}
                </div>
                <div class="text-xs text-gray-500">
                  Base: ${{ formatCurrency(item.precio) }}
                </div>
                <div class="text-xs text-gray-400">
                  {{ item.tipo_impuesto }} ({{ (item.valor_impuesto * 100).toFixed(0) }}%)
                </div>
              </div>
            </td>
            
            <!-- Proveedor/Lote -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex flex-col">
                <div class="text-sm text-gray-900">
                  {{ item.proveedor_nombre || 'Sin proveedor' }}
                </div>
                <div v-if="item.lote_codigo" class="text-xs text-gray-500">
                  Lote: {{ item.lote_codigo }}
                </div>
              </div>
            </td>
            
            <!-- Fecha -->
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ formatDate(item.fecha_ingreso) }}
            </td>
            
            <!-- Acciones -->
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" @click.stop>
              <div class="flex items-center justify-end space-x-2">
                <button
                  @click="verDetalle(item)"
                  class="text-indigo-600 hover:text-indigo-900 p-1 rounded"
                  title="Ver detalle"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
                
                <button
                  @click="editarItem(item)"
                  class="text-gray-600 hover:text-gray-900 p-1 rounded"
                  title="Editar"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </button>
                
                <button
                  @click="verMovimientos(item)"
                  class="text-gray-600 hover:text-gray-900 p-1 rounded"
                  title="Ver movimientos"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                  </svg>
                </button>
                
                <button
                  @click="eliminarItem(item)"
                  class="text-red-600 hover:text-red-900 p-1 rounded"
                  title="Eliminar"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface InventarioItem {
  id: number
  ruta_imagen: string | null
  nombre: string
  nombre_detallado: string
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
  sortBy: string
  sortOrder: 'asc' | 'desc'
}

const onRowCheckChange = (id: number, e: Event) => {
  const checked = (e.target as HTMLInputElement).checked
  toggleSelect(id, checked)
}

const props = defineProps<Props>()

// Emits
const emit = defineEmits<{
  select: [id: number, selected: boolean]
  'select-all': [selected: boolean]
  sort: [column: string]
  view: [item: InventarioItem]
  edit: [item: InventarioItem]
  movements: [item: InventarioItem]
  delete: [item: InventarioItem]
}>()

// Computed
const isAllSelected = computed(() => {
  return props.items.length > 0 && props.items.every(item => props.selectedItems.has(item.id))
})

const isPartiallySelected = computed(() => {
  const selectedCount = props.items.filter(item => props.selectedItems.has(item.id)).length
  return selectedCount > 0 && selectedCount < props.items.length
})

// Métodos
const toggleSelect = (id: number, selected: boolean) => {
  emit('select', id, selected)
}

const toggleSelectAll = (event: Event) => {
  const target = event.target as HTMLInputElement
  emit('select-all', target.checked)
}

const ordenar = (column: string) => {
  emit('sort', column)
}

const verDetalle = (item: InventarioItem) => {
  emit('view', item)
}

const editarItem = (item: InventarioItem) => {
  emit('edit', item)
}

const verMovimientos = (item: InventarioItem) => {
  emit('movements', item)
}

const eliminarItem = (item: InventarioItem) => {
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