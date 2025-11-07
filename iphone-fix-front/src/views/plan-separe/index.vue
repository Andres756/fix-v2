<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    <div class="max-w-7xl mx-auto space-y-6">
      <!-- Header -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Plan Separe</h1>
            <p class="text-gray-500 mt-1">Gestión de planes de apartado</p>
          </div>
          <button
            @click="showCrearModal = true"
            class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center space-x-2 shadow-lg"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Nuevo Plan</span>
          </button>
        </div>
      </div>

      <!-- Filtros -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Búsqueda -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Buscar
            </label>
            <div class="relative">
              <input
                v-model="filtros.q"
                type="text"
                placeholder="Buscar por código, cliente o producto..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                @input="debounceSearch"
              />
              <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </div>

          <!-- Filtro por Estado -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Estado
            </label>
            <select
              v-model="filtros.estado_id"
              @change="loadPlanes"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="">Todos los estados</option>
              <option v-for="estado in estados" :key="estado.id" :value="estado.id">
                {{ estado.nombre }}
              </option>
            </select>
          </div>

          <!-- Filtro por Fecha -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Fecha desde
            </label>
            <input
              v-model="filtros.desde"
              type="date"
              @change="loadPlanes"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
          </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
          <div class="flex items-center space-x-2">
            <button
              @click="limpiarFiltros"
              class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
            >
              Limpiar filtros
            </button>
            <button
              @click="loadPlanes"
              :disabled="isLoading"
              class="px-4 py-2 text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors disabled:opacity-50"
            >
              <svg v-if="isLoading" class="animate-spin h-5 w-5 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span v-else>Actualizar</span>
            </button>
          </div>

          <!-- Contador de resultados -->
          <div class="text-sm text-gray-600">
            <span class="font-medium">{{ totalPlanes }}</span> planes encontrados
          </div>
        </div>
      </div>

      <!-- Tabla -->
      <PlanSepareTable
        :planes="planes"
        :isLoading="isLoading"
        @create="showCrearModal = true"
        @view="handleVerDetalle"
        @abono="handleRegistrarAbono"
        @anular="handleAnular"
        @reasignar="handleReasignar"
      />

      <!-- Paginación -->
      <div v-if="totalPages > 1" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex items-center justify-between">
          <button
            @click="cambiarPagina(paginaActual - 1)"
            :disabled="paginaActual === 1"
            class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            Anterior
          </button>
          
          <div class="flex items-center space-x-2">
            <button
              v-for="page in paginasVisibles"
              :key="page"
              @click="cambiarPagina(page)"
              :class="[
                'px-4 py-2 rounded-lg transition-colors',
                page === paginaActual
                  ? 'bg-indigo-600 text-white'
                  : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
              ]"
            >
              {{ page }}
            </button>
          </div>

          <button
            @click="cambiarPagina(paginaActual + 1)"
            :disabled="paginaActual === totalPages"
            class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            Siguiente
          </button>
        </div>
      </div>
    </div>

    <!-- Modales -->
    <CrearPlanModal
      :open="showCrearModal"
      @close="showCrearModal = false"
      @created="handlePlanCreado"
    />

    <DetallePlanModal
      :open="showDetalleModal"
      :planId="planIdSeleccionado"
      @close="showDetalleModal = false"
    />

    <RegistrarAbonoModal
      :open="showAbonoModal"
      :planId="planIdSeleccionado"
      @close="showAbonoModal = false"
      @success="handleAbonoRegistrado"
    />

    <AnularPlanModal
      :open="showAnularModal"
      :planId="planIdSeleccionado"
      @close="showAnularModal = false"
      @success="handlePlanAnulado"
    />

    <!-- TODO: ReasignarPlanModal próximamente -->
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { toast } from 'vue3-toastify'
import type { PlanSepare, FiltrosPlanSepare } from '../../features/PlanSepare/types/planSepare'
import { fetchPlanesSepare } from '../../features/PlanSepare/api/planSepare'
import PlanSepareTable from '../../features/PlanSepare/components/PlanSepareTable.vue'
import CrearPlanModal from '../../features/PlanSepare/components/CrearPlanModal.vue'
import DetallePlanModal from '../../features/PlanSepare/components/DetallePlanModal.vue'
import RegistrarAbonoModal from '../../features/PlanSepare/components/RegistrarAbonoModal.vue'
import AnularPlanModal from '../../features/PlanSepare/components/AnularPlanModal.vue'


// Estado
const planes = ref<PlanSepare[]>([])
const isLoading = ref(false)
const showCrearModal = ref(false)
const showDetalleModal = ref(false)
const showAbonoModal = ref(false)
const showAnularModal = ref(false)
const planIdSeleccionado = ref<number | null>(null)

// Estados disponibles
const estados = ref([
  { id: 1, nombre: 'Abierto', codigo: 'ACT' },
  { id: 2, nombre: 'Asegurado', codigo: 'ASE' },
  { id: 3, nombre: 'Facturado', codigo: 'CER' },
  { id: 4, nombre: 'Expirado', codigo: 'EXP' },
  { id: 5, nombre: 'Cancelado', codigo: 'CAN' },
  { id: 6, nombre: 'Devuelto', codigo: 'DEV' },
])

// Filtros
const filtros = ref<FiltrosPlanSepare>({
  q: '',
  estado_id: undefined,
  desde: '',
  hasta: '',
  page: 1,
  per_page: 15
})

// Paginación
const paginaActual = ref(1)
const totalPlanes = ref(0)
const totalPages = ref(1)

const paginasVisibles = computed(() => {
  const pages: number[] = []
  const start = Math.max(1, paginaActual.value - 2)
  const end = Math.min(totalPages.value, paginaActual.value + 2)
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

// Debounce para búsqueda
let searchTimeout: ReturnType<typeof setTimeout>
function debounceSearch() {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    paginaActual.value = 1
    loadPlanes()
  }, 500)
}

// Cargar planes
async function loadPlanes() {
  isLoading.value = true
  
  try {
    const filtrosLimpios = {
      ...filtros.value,
      page: paginaActual.value,
      estado_id: filtros.value.estado_id || undefined,
      q: filtros.value.q || undefined,
      desde: filtros.value.desde || undefined,
      hasta: filtros.value.hasta || undefined
    }
    
    const response = await fetchPlanesSepare(filtrosLimpios)
    
    if (response.data) {
      planes.value = response.data
      totalPlanes.value = response.meta?.total || response.data.length
      totalPages.value = response.meta?.last_page || 1
      paginaActual.value = response.meta?.current_page || 1
    } else {
      planes.value = []
    }
  } catch (error: any) {
    console.error('Error cargando planes:', error)
    toast.error('Error al cargar los planes separe')
    planes.value = []
  } finally {
    isLoading.value = false
  }
}

function cambiarPagina(page: number) {
  if (page < 1 || page > totalPages.value) return
  paginaActual.value = page
  filtros.value.page = page
  loadPlanes()
}

function limpiarFiltros() {
  filtros.value = {
    q: '',
    estado_id: undefined,
    desde: '',
    hasta: '',
    page: 1,
    per_page: 15
  }
  paginaActual.value = 1
  loadPlanes()
}

// Handlers de eventos
function handlePlanCreado(plan: PlanSepare) {
  toast.success(`Plan ${plan.codigo} creado exitosamente`)
  loadPlanes()
}

function handleVerDetalle(planId: number) {
  planIdSeleccionado.value = planId
  showDetalleModal.value = true
}

function handleRegistrarAbono(planId: number) {
  planIdSeleccionado.value = planId
  showAbonoModal.value = true
}

function handleAbonoRegistrado() {
  toast.success('Abono registrado correctamente')
  loadPlanes()
}

function handleAnular(planId: number) {
  planIdSeleccionado.value = planId
  showAnularModal.value = true
}

function handlePlanAnulado() {
  toast.success('Plan anulado exitosamente')
  loadPlanes()
}

function handleReasignar(planId: number) {
  planIdSeleccionado.value = planId
  toast.info('Modal de reasignación próximamente...')
  // TODO: Implementar modal de reasignación
}

// Lifecycle
onMounted(() => {
  loadPlanes()
})
</script>