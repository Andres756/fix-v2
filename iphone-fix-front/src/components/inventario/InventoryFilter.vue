<template>
  <div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-medium text-gray-900">Filtros</h3>
      <button
        @click="toggleCollapsed"
        class="lg:hidden p-2 text-gray-400 hover:text-gray-500"
      >
        <svg 
          :class="['w-5 h-5 transform transition-transform', { 'rotate-180': !collapsed }]" 
          fill="none" 
          stroke="currentColor" 
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>
    </div>

    <div :class="['space-y-6', { 'hidden lg:block': collapsed }]">
      <!-- Tipo de Inventario -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Tipo de Inventario
        </label>
        <select
          v-model="localFilters.tipo_inventario_id"
          @change="onTipoChange"
          class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          :disabled="loading"
        >
          <option :value="null">Todos los tipos</option>
          <option 
            v-for="tipo in tiposInventario" 
            :key="tipo.id" 
            :value="tipo.id"
          >
            {{ tipo.nombre }}
          </option>
        </select>
      </div>

      <!-- Categoría -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Categoría
        </label>
        <select
          v-model="localFilters.categoria_id"
          class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          :disabled="loading || loadingCategorias"
        >
          <option :value="null">Todas las categorías</option>
          <option 
            v-for="categoria in categoriasDisponibles" 
            :key="categoria.id" 
            :value="categoria.id"
          >
            {{ categoria.nombre }}
          </option>
        </select>
        <div v-if="loadingCategorias" class="mt-1 text-xs text-gray-500">
          Cargando categorías...
        </div>
      </div>

      <!-- Estado de Inventario -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Estado
        </label>
        <select
          v-model="localFilters.estado_inventario_id"
          class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          :disabled="loading"
        >
          <option :value="null">Todos los estados</option>
          <option 
            v-for="estado in estadosInventario" 
            :key="estado.id" 
            :value="estado.id"
          >
            <span :class="getEstadoBadgeClass(estado.nombre)">
              {{ estado.nombre }}
            </span>
          </option>
        </select>
      </div>

      <!-- Proveedor -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Proveedor
        </label>
        <select
          v-model="localFilters.proveedor_id"
          class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          :disabled="loading"
        >
          <option :value="null">Todos los proveedores</option>
          <option 
            v-for="proveedor in proveedores" 
            :key="proveedor.id" 
            :value="proveedor.id"
          >
            {{ proveedor.nombre }}
          </option>
        </select>
      </div>

      <!-- Rango de Precios -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Rango de Precios (Final)
        </label>
        <div class="grid grid-cols-2 gap-2">
          <div>
            <input
              v-model.number="localFilters.precio_min"
              type="number"
              placeholder="Mín"
              class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
              :disabled="loading"
            />
          </div>
          <div>
            <input
              v-model.number="localFilters.precio_max"
              type="number"
              placeholder="Máx"
              class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
              :disabled="loading"
            />
          </div>
        </div>
      </div>

      <!-- Stock -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Stock
        </label>
        
        <!-- Checkbox solo sin stock -->
        <div class="mb-3">
          <label class="flex items-center">
            <input
              v-model="localFilters.solo_sin_stock"
              type="checkbox"
              class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              :disabled="loading"
            />
            <span class="ml-2 text-sm text-gray-700">Solo productos sin stock</span>
          </label>
        </div>

        <!-- Rango de stock (deshabilitado si solo_sin_stock está activo) -->
        <div class="grid grid-cols-2 gap-2" :class="{ 'opacity-50': localFilters.solo_sin_stock }">
          <div>
            <input
              v-model.number="localFilters.stock_min"
              type="number"
              placeholder="Stock mín"
              class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
              :disabled="loading || localFilters.solo_sin_stock"
            />
          </div>
          <div>
            <input
              v-model.number="localFilters.stock_max"
              type="number"
              placeholder="Stock máx"
              class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
              :disabled="loading || localFilters.solo_sin_stock"
            />
          </div>
        </div>
      </div>

      <!-- Fecha de Ingreso -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Fecha de Ingreso
        </label>
        <div class="grid grid-cols-2 gap-2">
          <div>
            <input
              v-model="localFilters.fecha_desde"
              type="date"
              class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
              :disabled="loading"
            />
          </div>
          <div>
            <input
              v-model="localFilters.fecha_hasta"
              type="date"
              class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
              :disabled="loading"
            />
          </div>
        </div>
      </div>

      <!-- Solo Activos -->
      <div>
        <label class="flex items-center">
          <input
            v-model="localFilters.solo_activos"
            type="checkbox"
            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            :disabled="loading"
          />
          <span class="ml-2 text-sm text-gray-700">Solo productos activos</span>
        </label>
      </div>

      <!-- Botones de acción -->
      <div class="flex flex-col space-y-2 pt-4 border-t border-gray-200">
        <button
          @click="aplicarFiltros"
          :disabled="loading"
          class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ loading ? 'Aplicando...' : 'Aplicar Filtros' }}
        </button>
        
        <button
          @click="limpiarFiltros"
          :disabled="loading"
          class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
          Limpiar Todo
        </button>
      </div>

      <!-- Contador de filtros activos -->
      <div v-if="filtrosActivosCount > 0" class="text-center pt-2 border-t border-gray-200">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
          {{ filtrosActivosCount }} filtro{{ filtrosActivosCount !== 1 ? 's' : '' }} activo{{ filtrosActivosCount !== 1 ? 's' : '' }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'

interface Filters {
  tipo_inventario_id: number | null
  categoria_id: number | null
  estado_inventario_id: number | null
  proveedor_id: number | null
  precio_min: number | null
  precio_max: number | null
  stock_min: number | null
  stock_max: number | null
  solo_sin_stock: boolean
  solo_activos: boolean
  fecha_desde: string | null
  fecha_hasta: string | null
}

interface TipoInventario {
  id: number
  nombre: string
}

interface Categoria {
  id: number
  nombre: string
  tipo_inventario_id: number
}

interface EstadoInventario {
  id: number
  nombre: string
}

interface Proveedor {
  id: number
  nombre: string
}

// Props
interface Props {
  filters: Filters
  loading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  loading: false
})

// Emits
const emit = defineEmits<{
  'update:filters': [filters: Filters]
  apply: []
  clear: []
}>()

// Estado local
const collapsed = ref(false)
const loadingCategorias = ref(false)
const localFilters = ref<Filters>({ ...props.filters })

// Datos de catálogos
const tiposInventario = ref<TipoInventario[]>([])
const categorias = ref<Categoria[]>([])
const estadosInventario = ref<EstadoInventario[]>([])
const proveedores = ref<Proveedor[]>([])

// Computed
const categoriasDisponibles = computed(() => {
  if (!localFilters.value.tipo_inventario_id) {
    return categorias.value
  }
  return categorias.value.filter(
    cat => cat.tipo_inventario_id === localFilters.value.tipo_inventario_id
  )
})

const filtrosActivosCount = computed(() => {
  let count = 0
  Object.entries(localFilters.value).forEach(([key, value]) => {
    if (key === 'solo_sin_stock' || key === 'solo_activos') {
      if (value === true) count++
    } else if (value !== null && value !== '') {
      count++
    }
  })
  return count
})

// Métodos
const toggleCollapsed = () => {
  collapsed.value = !collapsed.value
}

const getEstadoBadgeClass = (estadoNombre: string) => {
  const estado = estadoNombre.toLowerCase()
  if (estado.includes('stock') || estado.includes('disponible')) {
    return 'text-green-800'
  } else if (estado.includes('sin stock') || estado.includes('agotado')) {
    return 'text-red-800'
  } else if (estado.includes('reservado') || estado.includes('pendiente')) {
    return 'text-yellow-800'
  } else if (estado.includes('dañado') || estado.includes('defectuoso')) {
    return 'text-red-800'
  }
  return 'text-gray-800'
}

const onTipoChange = () => {
  // Limpiar categoría cuando cambia el tipo
  localFilters.value.categoria_id = null
  cargarCategorias()
}

const aplicarFiltros = () => {
  emit('update:filters', { ...localFilters.value })
  emit('apply')
}

const limpiarFiltros = () => {
  localFilters.value = {
    tipo_inventario_id: null,
    categoria_id: null,
    estado_inventario_id: null,
    proveedor_id: null,
    precio_min: null,
    precio_max: null,
    stock_min: null,
    stock_max: null,
    solo_sin_stock: false,
    solo_activos: false,
    fecha_desde: null,
    fecha_hasta: null
  }
  emit('update:filters', { ...localFilters.value })
  emit('clear')
}

// Métodos de carga de datos
const cargarTiposInventario = async () => {
  try {
    // Simulación de API call
    await new Promise(resolve => setTimeout(resolve, 200))
    
    tiposInventario.value = [
      { id: 1, nombre: 'Electrónicos' },
      { id: 2, nombre: 'Mobiliario' },
      { id: 3, nombre: 'Suministros' }
    ]
  } catch (error) {
    console.error('Error cargando tipos de inventario:', error)
  }
}

const cargarCategorias = async () => {
  try {
    loadingCategorias.value = true
    
    // Simulación de API call
    await new Promise(resolve => setTimeout(resolve, 300))
    
    categorias.value = [
      { id: 1, nombre: 'Computadoras', tipo_inventario_id: 1 },
      { id: 2, nombre: 'Periféricos', tipo_inventario_id: 1 },
      { id: 3, nombre: 'Accesorios', tipo_inventario_id: 1 },
      { id: 4, nombre: 'Escritorios', tipo_inventario_id: 2 },
      { id: 5, nombre: 'Sillas', tipo_inventario_id: 2 },
      { id: 6, nombre: 'Estanterías', tipo_inventario_id: 2 },
      { id: 7, nombre: 'Papelería', tipo_inventario_id: 3 },
      { id: 8, nombre: 'Limpieza', tipo_inventario_id: 3 }
    ]
  } catch (error) {
    console.error('Error cargando categorías:', error)
  } finally {
    loadingCategorias.value = false
  }
}

const cargarEstadosInventario = async () => {
  try {
    // Simulación de API call
    await new Promise(resolve => setTimeout(resolve, 200))
    
    estadosInventario.value = [
      { id: 1, nombre: 'En Stock' },
      { id: 2, nombre: 'Reservado' },
      { id: 3, nombre: 'Sin Stock' },
      { id: 4, nombre: 'Dañado' },
      { id: 5, nombre: 'En Tránsito' }
    ]
  } catch (error) {
    console.error('Error cargando estados de inventario:', error)
  }
}

const cargarProveedores = async () => {
  try {
    // Simulación de API call
    await new Promise(resolve => setTimeout(resolve, 200))
    
    proveedores.value = [
      { id: 1, nombre: 'TechCorp SA' },
      { id: 2, nombre: 'PerifericosPro' },
      { id: 3, nombre: 'MueblesBogotá' },
      { id: 4, nombre: 'SuministrosOficina' }
    ]
  } catch (error) {
    console.error('Error cargando proveedores:', error)
  }
}

// Watchers
watch(
  () => props.filters,
  (newFilters) => {
    localFilters.value = { ...newFilters }
  },
  { deep: true }
)

watch(
  () => localFilters.value.solo_sin_stock,
  (nuevoValor) => {
    if (nuevoValor) {
      // Si se activa "solo sin stock", limpiar rangos de stock
      localFilters.value.stock_min = null
      localFilters.value.stock_max = null
    }
  }
)

// Lifecycle
onMounted(async () => {
  await Promise.all([
    cargarTiposInventario(),
    cargarCategorias(),
    cargarEstadosInventario(),
    cargarProveedores()
  ])
})
</script>