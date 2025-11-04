<!-- views/facturacion/index.vue -->
<template>
  <div class="min-h-screen bg-gray-50 p-4 lg:p-6">
    <!-- Header -->
    <div class="mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Facturación</h1>
          <p class="mt-1 text-sm text-gray-600">Gestiona facturas, pagos y anulaciones</p>
        </div>
        <button
          @click="showNuevaFactura = true"
          class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center gap-2 shadow-sm"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Nueva Factura
        </button>
      </div>
    </div>

    <!-- Tarjetas de resumen -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">Ventas del Día</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">
              {{ formatMoney(resumen.total_ventas_dia) }}
            </p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">Ventas del Mes</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">
              {{ formatMoney(resumen.total_ventas_mes) }}
            </p>
          </div>
          <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">Facturas Pendientes</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ resumen.facturas_pendientes }}</p>
          </div>
          <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">Anuladas este Mes</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ resumen.facturas_anuladas_mes }}</p>
          </div>
          <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-200">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Búsqueda -->
        <div class="lg:col-span-2">
          <div class="relative">
            <input
              v-model="filters.q"
              type="text"
              placeholder="Buscar por código, cliente..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              @input="debouncedSearch"
            />
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
        </div>

        <!-- Estado -->
        <div>
          <select
            v-model="filters.estado"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            @change="loadFacturas"
          >
            <option value="">Todos los estados</option>
            <option value="PEND">Pendiente</option>
            <option value="PARC">Parcial</option>
            <option value="PAGA">Pagada</option>
            <option value="ANUL">Anulada</option>
          </select>
        </div>

        <!-- Tipo -->
        <div>
          <select
            v-model="filters.tipo_venta"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            @change="loadFacturas"
          >
            <option value="">Todos los tipos</option>
            <option value="VD">Venta Directa</option>
            <option value="SRV">Servicio</option>
            <option value="PS">Plan Separe</option>
          </select>
        </div>

        <!-- Botón limpiar -->
        <div class="flex items-center">
          <button
            @click="clearFilters"
            class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
          >
            Limpiar Filtros
          </button>
        </div>
      </div>

      <!-- Filtros adicionales (ocultos por defecto) -->
      <div v-if="showAdvancedFilters" class="mt-4 pt-4 border-t border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <!-- Fecha desde -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
            <input
              v-model="filters.desde"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              @change="loadFacturas"
            />
          </div>

          <!-- Fecha hasta -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
            <input
              v-model="filters.hasta"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              @change="loadFacturas"
            />
          </div>

          <!-- Checkbox prefacturas -->
          <div class="flex items-end">
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                v-model="filters.es_prefactura"
                type="checkbox"
                class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                @change="loadFacturas"
              />
              <span class="text-sm text-gray-700">Solo Prefacturas</span>
            </label>
          </div>
        </div>
      </div>

      <!-- Toggle filtros avanzados -->
      <div class="mt-3 text-center">
        <button
          @click="showAdvancedFilters = !showAdvancedFilters"
          class="text-sm text-blue-600 hover:text-blue-700 font-medium"
        >
          {{ showAdvancedFilters ? 'Ocultar' : 'Mostrar' }} filtros avanzados
          <svg 
            class="inline-block w-4 h-4 ml-1 transition-transform duration-200"
            :class="showAdvancedFilters ? 'rotate-180' : ''"
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Tabla de facturas -->
    <FacturasTable
      :facturas="facturas"
      :isLoading="isLoading"
      @view="handleView"
      @print="handlePrint"
      @pagar="handlePagar"
      @anular="handleAnular"
      @convertir="handleConvertir"
      @create-factura="showNuevaFactura = true"
    />

    <!-- Paginación -->
    <div v-if="meta && meta.last_page > 1" class="mt-6">
      <div class="flex items-center justify-between">
        <div class="text-sm text-gray-600">
          Mostrando {{ meta.from || 0 }} a {{ meta.to || 0 }} de {{ meta.total || 0 }} resultados
        </div>
        <div class="flex gap-2">
          <button
            @click="goToPage(currentPage - 1)"
            :disabled="currentPage <= 1"
            class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:bg-gray-100 disabled:text-gray-400 transition-colors"
          >
            Anterior
          </button>
          
          <div class="flex gap-1">
            <button
              v-for="page in visiblePages"
              :key="page"
              @click="goToPage(page)"
              :class="[
                'px-3 py-2 rounded-lg transition-colors',
                page === currentPage
                  ? 'bg-blue-600 text-white'
                  : 'bg-white border border-gray-300 hover:bg-gray-50'
              ]"
            >
              {{ page }}
            </button>
          </div>

          <button
            @click="goToPage(currentPage + 1)"
            :disabled="currentPage >= (meta?.last_page || 1)"
            class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:bg-gray-100 disabled:text-gray-400 transition-colors"
          >
            Siguiente
          </button>
        </div>
      </div>
    </div>

    <!-- Modales -->
    <NuevaFacturaModal
      :open="showNuevaFactura"
      @close="showNuevaFactura = false"
      @success="onFacturaCreated"
    />

    <PagoModal
      :open="showPagoModal"
      :facturaId="selectedFacturaId"
      @close="showPagoModal = false"
      @success="onPagoRegistrado"
    />

    <AnularFacturaModal
      :open="showAnularModal"
      :facturaId="selectedFacturaId"
      @close="showAnularModal = false"
      @success="onFacturaAnulada"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

// Componentes
import FacturasTable from '../../features/Facturacion/components/FacturasTable.vue'
import NuevaFacturaModal from '../../features/Facturacion/components/NuevaFacturaModal.vue'
import PagoModal from '../../features/Facturacion/components/PagoModal.vue'
import AnularFacturaModal from '../../features/Facturacion/components/AnularFacturaModal.vue'

// API
import { 
  fetchFacturas, 
  fetchResumenFacturacion,
  getFacturaPrintUrl,
  convertirPrefactura
} from '../../features/Facturacion/api/facturacion'
import type { Factura, FiltrosFactura, ResumenFacturacion } from '../../features/Facturacion/types/factura'

// Estado
const isLoading = ref(false)
const facturas = ref<Factura[]>([])
const meta = ref<any>(null)
const currentPage = ref(1)
const showAdvancedFilters = ref(false)

// Resumen
const resumen = ref<ResumenFacturacion>({
  total_ventas_dia: 0,
  total_ventas_mes: 0,
  facturas_pendientes: 0,
  facturas_anuladas_mes: 0
})

// Filtros
const filters = reactive<FiltrosFactura>({
  q: '',
  estado: '',
  tipo_venta: '',
  desde: '',
  hasta: '',
  es_prefactura: false
})

// Modales
const showNuevaFactura = ref(false)
const showPagoModal = ref(false)
const showAnularModal = ref(false)
const selectedFacturaId = ref<number | null>(null)

// Computed
const visiblePages = computed(() => {
  const pages = []
  const total = meta.value?.last_page || 1
  const current = currentPage.value
  
  let start = Math.max(1, current - 2)
  let end = Math.min(total, current + 2)
  
  if (current <= 3) {
    end = Math.min(5, total)
  }
  if (current >= total - 2) {
    start = Math.max(1, total - 4)
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

// Métodos
const loadFacturas = async () => {
  try {
    isLoading.value = true
    
    const response = await fetchFacturas({
      ...filters,
      page: currentPage.value,
      per_page: 20
    })
    
    facturas.value = response.data || []
    meta.value = response.meta
  } catch (error) {
    console.error('Error loading facturas:', error)
    toast.error('Error al cargar las facturas')
  } finally {
    isLoading.value = false
  }
}

const loadResumen = async () => {
  try {
    resumen.value = await fetchResumenFacturacion()
  } catch (error) {
    console.error('Error loading resumen:', error)
  }
}

// Debounce para búsqueda
let searchTimeout: any = null
const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    currentPage.value = 1
    loadFacturas()
  }, 500)
}

const clearFilters = () => {
  filters.q = ''
  filters.estado = ''
  filters.tipo_venta = ''
  filters.desde = ''
  filters.hasta = ''
  filters.es_prefactura = false
  currentPage.value = 1
  loadFacturas()
}

const goToPage = (page: number) => {
  if (page < 1 || page > (meta.value?.last_page || 1)) return
  currentPage.value = page
  loadFacturas()
}

// Handlers
const handleView = (facturaId: number) => {
  // TODO: Implementar modal de detalle
  console.log('Ver factura:', facturaId)
}

const handlePrint = async (facturaId: number) => {
  try {
    const url = await getFacturaPrintUrl(facturaId)
    window.open(url, '_blank')
  } catch (error) {
    toast.error('Error al obtener la factura para imprimir')
  }
}

const handlePagar = (facturaId: number) => {
  selectedFacturaId.value = facturaId
  showPagoModal.value = true
}

const handleAnular = (facturaId: number) => {
  selectedFacturaId.value = facturaId
  showAnularModal.value = true
}

const handleConvertir = async (prefacturaId: number) => {
  if (!confirm('¿Desea convertir esta prefactura en factura?')) return
  
  try {
    await convertirPrefactura(prefacturaId)
    toast.success('Prefactura convertida exitosamente')
    loadFacturas()
  } catch (error) {
    toast.error('Error al convertir la prefactura')
  }
}

// Callbacks de modales
const onFacturaCreated = () => {
  loadFacturas()
  loadResumen()
}

const onPagoRegistrado = () => {
  loadFacturas()
  loadResumen()
}

const onFacturaAnulada = () => {
  loadFacturas()
  loadResumen()
}

// Helpers
const formatMoney = (amount: number): string => {
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount)
}

// Lifecycle
onMounted(() => {
  loadFacturas()
  loadResumen()
})
</script>