<template>
  <!-- Overlay -->
  <div
    v-if="open"
    class="fixed inset-0 overflow-hidden z-50"
    aria-labelledby="slide-over-title"
    role="dialog"
    aria-modal="true"
  >
    <div class="absolute inset-0 overflow-hidden">
      <!-- Background overlay -->
      <div
        class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        @click="cerrar"
      ></div>

      <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
        <div class="w-screen max-w-2xl">
          <div class="h-full flex flex-col bg-white shadow-xl overflow-y-scroll">
            <!-- Header -->
            <div class="px-4 py-6 bg-gray-50 border-b border-gray-200 sm:px-6">
              <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900" id="slide-over-title">
                  {{ loading ? 'Cargando...' : (item ? 'Detalle del Producto' : 'Sin información') }}
                </h2>
                <div class="ml-3 h-7 flex items-center">
                  <button
                    @click="cerrar"
                    type="button"
                    class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  >
                    <span class="sr-only">Cerrar panel</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Content -->
            <div class="flex-1 p-6">
              <!-- Loading state -->
              <div v-if="loading" class="space-y-6">
                <div class="animate-pulse space-y-4">
                  <div class="w-full h-64 bg-gray-300 rounded-lg"></div>
                  <div class="space-y-3">
                    <div class="h-6 bg-gray-300 rounded w-3/4"></div>
                    <div class="h-4 bg-gray-300 rounded w-1/2"></div>
                    <div class="h-4 bg-gray-300 rounded w-2/3"></div>
                  </div>
                </div>
              </div>

              <!-- Content when item is loaded -->
              <div v-else-if="item" class="space-y-6">
                <!-- Imagen principal -->
                <div class="w-full h-64 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center">
                  <img
                    v-if="item.ruta_imagen"
                    :src="item.ruta_imagen"
                    :alt="item.nombre"
                    class="w-full h-full object-cover"
                    @error="handleImageError"
                  />
                  <svg
                    v-else
                    class="w-20 h-20 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>

                <!-- Información básica -->
                <div>
                  <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ item.nombre }}</h3>
                  <p class="text-lg text-gray-600 mb-1">{{ item.codigo }}</p>
                  <p v-if="item.nombre_detallado !== item.nombre" class="text-sm text-gray-500">
                    {{ item.nombre_detallado }}
                  </p>
                  
                  <!-- Estado activo/inactivo -->
                  <div class="mt-3">
                    <span :class="[
                      'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                      item.activo 
                        ? 'bg-green-100 text-green-800'
                        : 'bg-red-100 text-red-800'
                    ]">
                      <div :class="[
                        'w-2 h-2 rounded-full mr-2',
                        item.activo ? 'bg-green-400' : 'bg-red-400'
                      ]"></div>
                      {{ item.activo ? 'Activo' : 'Inactivo' }}
                    </span>
                  </div>
                </div>

                <!-- Precios -->
                <div class="bg-gray-50 rounded-lg p-4">
                  <h4 class="text-lg font-semibold text-gray-900 mb-3">Información de Precios</h4>
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Precio Base</label>
                      <p class="text-2xl font-bold text-gray-900">${{ formatCurrency(item.precio) }}</p>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700">
                        Precio Final
                        <span class="text-xs text-gray-500">(con {{ item.tipo_impuesto }})</span>
                      </label>
                      <p class="text-2xl font-bold text-indigo-600">${{ formatCurrency(item.precio_final) }}</p>
                    </div>
                  </div>
                  <div class="mt-3 pt-3 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                      <span class="text-sm text-gray-600">{{ item.tipo_impuesto }} ({{ (item.valor_impuesto * 100).toFixed(0) }}%)</span>
                      <span class="text-sm font-medium text-gray-900">${{ formatCurrency(item.precio_final - item.precio) }}</span>
                    </div>
                    <div class="flex justify-between items-center mt-1">
                      <span class="text-sm text-gray-600">Margen</span>
                      <span class="text-sm font-medium text-green-600">
                        {{ calcularMargen(item.precio, item.precio_final) }}%
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Inventario -->
                <div class="bg-gray-50 rounded-lg p-4">
                  <h4 class="text-lg font-semibold text-gray-900 mb-3">Inventario</h4>
                  <div class="space-y-3">
                    <!-- Stock actual -->
                    <div class="flex items-center justify-between">
                      <span class="text-sm font-medium text-gray-700">Stock Actual</span>
                      <span :class="[
                        'text-lg font-bold',
                        getStockColorClass(item.stock, item.stock_minimo)
                      ]">
                        {{ item.stock }}
                      </span>
                    </div>

                    <!-- Stock mínimo -->
                    <div class="flex items-center justify-between">
                      <span class="text-sm font-medium text-gray-700">Stock Mínimo</span>
                      <span class="text-lg font-semibold text-gray-600">{{ item.stock_minimo }}</span>
                    </div>

                    <!-- Barra de progreso -->
                    <div>
                      <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-gray-500">Nivel de stock</span>
                        <span class="text-xs text-gray-500">
                          {{ item.stock <= item.stock_minimo ? 'Crítico' : 'Normal' }}
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

                    <!-- Estado de inventario -->
                    <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                      <span class="text-sm font-medium text-gray-700">Estado</span>
                      <span :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        getEstadoBadgeClass(item.estado_inventario_nombre)
                      ]">
                        {{ item.estado_inventario_nombre }}
                      </span>
                    </div>

                    <!-- Fecha de ingreso -->
                    <div class="flex items-center justify-between">
                      <span class="text-sm font-medium text-gray-700">Fecha de Ingreso</span>
                      <span class="text-sm text-gray-600">{{ formatDate(item.fecha_ingreso) }}</span>
                    </div>
                  </div>
                </div>

                <!-- Clasificación -->
                <div class="bg-gray-50 rounded-lg p-4">
                  <h4 class="text-lg font-semibold text-gray-900 mb-3">Clasificación</h4>
                  <div class="space-y-3">
                    <div class="flex items-center justify-between">
                      <span class="text-sm font-medium text-gray-700">Tipo de Inventario</span>
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ item.tipo_inventario_nombre }}
                      </span>
                    </div>
                    <div class="flex items-center justify-between">
                      <span class="text-sm font-medium text-gray-700">Categoría</span>
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ item.categoria_nombre }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Proveedor y Lote -->
                <div v-if="item.proveedor_nombre || item.lote_codigo" class="bg-gray-50 rounded-lg p-4">
                  <h4 class="text-lg font-semibold text-gray-900 mb-3">Información Adicional</h4>
                  <div class="space-y-3">
                    <div v-if="item.proveedor_nombre" class="flex items-center justify-between">
                      <span class="text-sm font-medium text-gray-700">Proveedor</span>
                      <span class="text-sm text-gray-900">{{ item.proveedor_nombre }}</span>
                    </div>
                    <div v-if="item.lote_codigo" class="flex items-center justify-between">
                      <span class="text-sm font-medium text-gray-700">Lote</span>
                      <span class="text-sm text-gray-900">{{ item.lote_codigo }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- No item state -->
              <div v-else class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Sin información</h3>
                <p class="mt-1 text-sm text-gray-500">No se pudo cargar la información del producto.</p>
              </div>
            </div>

            <!-- Actions Footer -->
            <div v-if="item && !loading" class="border-t border-gray-200 px-6 py-4">
              <div class="flex flex-col space-y-3">
                <!-- Acciones principales -->
                <div class="flex space-x-3">
                  <button
                    @click="editarItem"
                    class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar
                  </button>
                  <button
                    @click="verMovimientos"
                    class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Movimientos
                  </button>
                </div>

                <!-- Acciones secundarias -->
                <div class="flex space-x-3">
                  <button
                    @click="duplicarItem"
                    class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    Duplicar
                  </button>
                  <button
                    @click="toggleActivo"
                    :class="[
                      'flex-1 inline-flex justify-center items-center px-4 py-2 border text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2',
                      item.activo
                        ? 'border-red-300 text-red-700 bg-white hover:bg-red-50 focus:ring-red-500'
                        : 'border-green-300 text-green-700 bg-white hover:bg-green-50 focus:ring-green-500'
                    ]"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path 
                        v-if="item.activo"
                        stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="2" 
                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636" 
                      />
                      <path 
                        v-else
                        stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="2" 
                        d="M5 13l4 4L19 7" 
                      />
                    </svg>
                    {{ item.activo ? 'Desactivar' : 'Activar' }}
                  </button>
                </div>

                <!-- Registro de movimientos -->
                <div class="pt-3 border-t border-gray-200">
                  <div class="text-sm font-medium text-gray-700 mb-2">Registrar Movimiento</div>
                  <div class="flex space-x-3">
                    <button
                      @click="registrarMovimiento('entrada')"
                      class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-green-300 text-sm font-medium rounded-md shadow-sm text-green-700 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    >
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                      </svg>
                      Entrada
                    </button>
                    <button
                      @click="registrarMovimiento('salida')"
                      :disabled="item.stock === 0"
                      class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-orange-300 text-sm font-medium rounded-md shadow-sm text-orange-700 bg-white hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                      </svg>
                      Salida
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { watch } from 'vue'

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
  open: boolean
  item: InventarioItem | null
  loading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  loading: false
})

// Emits
const emit = defineEmits<{
  'update:open': [open: boolean]
  edit: [item: InventarioItem]
  movements: [item: InventarioItem]
  duplicate: [item: InventarioItem]
  'toggle-active': [item: InventarioItem]
  'register-movement': [item: InventarioItem, tipo: 'entrada' | 'salida']
}>()

// Métodos
const cerrar = () => {
  emit('update:open', false)
}

const editarItem = () => {
  if (props.item) {
    emit('edit', props.item)
  }
}

const verMovimientos = () => {
  if (props.item) {
    emit('movements', props.item)
  }
}

const duplicarItem = () => {
  if (props.item) {
    emit('duplicate', props.item)
  }
}

const toggleActivo = () => {
  if (props.item) {
    emit('toggle-active', props.item)
  }
}

const registrarMovimiento = (tipo: 'entrada' | 'salida') => {
  if (props.item) {
    emit('register-movement', props.item, tipo)
  }
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
    month: 'long',
    day: 'numeric'
  })
}

const calcularMargen = (precioBase: number, precioFinal: number) => {
  if (precioBase === 0) return 0
  return (((precioFinal - precioBase) / precioBase) * 100).toFixed(1)
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

// Watchers
watch(() => props.open, (isOpen) => {
  if (isOpen) {
    // Prevenir scroll del body cuando el drawer está abierto
    document.body.style.overflow = 'hidden'
  } else {
    // Restaurar scroll del body cuando el drawer se cierra
    document.body.style.overflow = 'unset'
  }
})
</script>