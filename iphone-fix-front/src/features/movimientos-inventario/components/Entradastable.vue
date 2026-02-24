<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200">
  <!-- Header con botón Nueva Entrada -->
  <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
    <div>
      <h2 class="text-lg font-semibold text-gray-900">Entradas de Inventario</h2>
      <p class="text-sm text-gray-500 mt-1">Gestiona las entradas de productos al inventario</p>
    </div>
    <div class="flex gap-2">
      <!-- NUEVO: Botón Crear Cliente -->
      <button
        @click="modalCrearCliente = true"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        Nuevo Cliente
      </button>
      
      <button
        @click="emit('nueva-entrada')"
        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Nueva Entrada
      </button>
    </div>
  </div>

    <!-- Barra de Búsqueda -->
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="relative">
        <input
          v-model="searchQuery"
          @input="buscar"
          type="text"
          placeholder="Buscar por código, nombre, proveedor, cliente o lote..."
          class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
        />
        <svg
          class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
          />
        </svg>
        
        <!-- Botón limpiar -->
        <button
          v-if="searchQuery"
          @click="limpiarBusqueda"
          class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <!-- Indicadores de búsqueda -->
      <div v-if="searching" class="mt-2 text-sm text-blue-600">
        🔍 Buscando "{{ searchQuery }}"...
      </div>
      <div v-else-if="searchQuery && entradas.length > 0" class="mt-2 text-sm text-green-600">
        ✅ {{ entradas.length }} resultado(s) encontrado(s)
      </div>
    </div>

    <!-- Filtros -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
          <select
            v-model="filtros.estado_entrada_id"
            @change="aplicarFiltros"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
          >
            <option :value="null">Todos</option>
            <option v-for="estado in estadosEntrada" :key="estado.id" :value="estado.id">
              {{ estado.nombre }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
          <input
            v-model="filtros.fecha_desde"
            @change="aplicarFiltros"
            type="date"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
          <input
            v-model="filtros.fecha_hasta"
            @change="aplicarFiltros"
            type="date"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
          />
        </div>

        <div class="flex items-end">
          <button
            @click="limpiarFiltros"
            class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
          >
            Limpiar Filtros
          </button>
        </div>
      </div>
    </div>

    <!-- Tabla -->
    <div class="overflow-x-auto">
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600"></div>
      </div>

      <div v-else-if="entradas.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay entradas</h3>
        <p class="mt-1 text-sm text-gray-500">No se encontraron entradas con los filtros seleccionados.</p>
      </div>

      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              #ID / Fecha
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Origen
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Lote
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Estado
            </th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
              Items
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Total
            </th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
              Acciones
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <template v-for="entrada in entradas" :key="entrada.id">
            <!-- Fila principal -->
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">#{{ entrada.id }}</div>
                <div class="text-sm text-gray-500">{{ formatDate(entrada.fecha_entrada) }}</div>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-gray-900">
                  {{ entrada.proveedor?.nombre || entrada.cliente?.nombre || 'N/A' }}
                </div>
                <div class="text-sm text-gray-500">
                  <span v-if="entrada.tipo_entrada === 'proveedor'" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                    Proveedor
                  </span>
                  <span v-else class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                    Cliente
                  </span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <span v-if="entrada.lote" class="text-gray-900">
                  {{ entrada.lote.numero_lote }}
                </span>
                <span v-else class="text-gray-400 italic">Sin asignar</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :style="{ backgroundColor: entrada.estado_entrada?.color || '#6B7280' }"
                  class="px-2 py-1 text-xs font-medium rounded-full text-white"
                >
                  {{ entrada.estado_entrada?.nombre || 'N/A' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                {{ entrada.items?.length || 0 }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                {{ formatCurrency(calcularTotal(entrada)) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                <div class="flex items-center justify-center gap-2">
                  <!-- Ver detalle -->
                  <button
                    @click="toggleDetalle(entrada.id)"
                    class="text-gray-600 hover:text-gray-900"
                    title="Ver detalle"
                  >
                    <svg
                      class="w-5 h-5 transition-transform"
                      :class="{ 'rotate-180': detallesAbiertos.has(entrada.id) }"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                  
                  <!-- Cambiar Estado -->
                  <button
                    @click="abrirModalCambiarEstado(entrada)"
                    class="text-orange-600 hover:text-orange-700"
                    title="Cambiar estado"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                  </button>
                  
                  <!-- Asignar/Editar Lote -->
                  <button
                    v-if="!entrada.lote_id"
                    @click="emit('asignar-lote', entrada)"
                    class="text-purple-600 hover:text-purple-900"
                    title="Asignar lote"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                  </button>
                  <button
                    v-else
                    @click="emit('asignar-lote', entrada)"
                    class="text-blue-600 hover:text-blue-900"
                    title="Editar distribución"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </button>
                  
                  <!-- Costos Adicionales -->
                  <button
                    v-if="entrada.tipo_entrada === 'cliente'"
                    @click="abrirModalCostos(entrada)"
                    class="text-purple-600 hover:text-purple-700"
                    title="Agregar costos adicionales"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2 7h20M2 11h20M5 15h2m3 0h9m2-8H2v10a2 2 0 002 2h16a2 2 0 002-2V7z"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>

            <!-- Fila expandible -->
            <tr v-if="detallesAbiertos.has(entrada.id)">
              <td colspan="7" class="px-6 py-4 bg-gray-50">
                <div class="text-sm">
                  <h4 class="font-semibold text-gray-900 mb-3">Items de la entrada</h4>
                  <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                      <thead>
                        <tr class="bg-gray-100">
                          <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                          <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                          <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Costo Unit.</th>
                          <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Flete Asig.</th>
                          <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total Item</th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="item in entrada.items" :key="item.id">
                          <td class="px-3 py-2 text-sm text-gray-900">
                            {{ item.inventario?.nombre }}
                            <span class="text-gray-500 text-xs block">{{ item.inventario?.codigo }}</span>
                          </td>
                          <td class="px-3 py-2 text-sm text-center text-gray-900">
                            {{ item.cantidad }}
                          </td>
                          <td class="px-3 py-2 text-sm text-right text-gray-900">
                            {{ formatCurrency(item.costo_unitario) }}
                          </td>
                          <td class="px-3 py-2 text-sm text-right text-gray-600">
                            {{ formatCurrency(item.costo_flete_asignado || 0) }}
                          </td>
                          <td class="px-3 py-2 text-sm text-right font-medium text-gray-900">
                            {{ formatCurrency(item.costo_total_item || (item.costo_unitario * item.cantidad)) }}
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                  <div v-if="entrada.observaciones" class="mt-3 text-sm">
                    <span class="text-gray-500 font-medium">Observaciones:</span>
                    <p class="text-gray-700 mt-1">{{ entrada.observaciones }}</p>
                  </div>
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <!-- Paginación -->
    <div
      v-if="pagination && pagination.total > 0"
      class="px-6 py-4 border-t border-gray-200 bg-gray-50"
    >
      <div class="flex items-center justify-between">
        <div class="text-sm text-gray-700">
          Mostrando
          <span class="font-medium">{{ pagination.from }}</span>
          a
          <span class="font-medium">{{ pagination.to }}</span>
          de
          <span class="font-medium">{{ pagination.total }}</span>
          entradas
        </div>

        <div class="flex gap-2">
          <button
            @click="cambiarPagina(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="px-3 py-1 border border-gray-300 rounded-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-100"
          >
            Anterior
          </button>
          <button
            @click="cambiarPagina(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="px-3 py-1 border border-gray-300 rounded-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-100"
          >
            Siguiente
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Costos Adicionales -->
  <CostosAdicionalesModal
    :is-open="modalCostos.isOpen"
    :entrada="modalCostos.entrada"
    @close="cerrarModalCostos"
    @success="handleCostosActualizados"
  />

  <!-- Modal Cambiar Estado -->
  <CambiarEstadoModal
    :is-open="modalCambiarEstado.isOpen"
    :entrada="modalCambiarEstado.entrada"
    @close="cerrarModalCambiarEstado"
    @success="handleEstadoCambiado"
  />

  <!-- Modal Crear Cliente -->
  <ClienteModal
    :open="modalCrearCliente"
    @close="modalCrearCliente = false"
    @created="handleClienteCreado"
  />
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { toast } from 'vue3-toastify'
import http from '../../../shared/api/http'
import { fetchEntradasInventario, fetchEstadosEntrada } from '../../inventario/api/inventoryEntries'
import type { EntradaInventario, EstadoEntrada } from '../../inventario/types/inventoryEntry'
import type { Paginated } from '../../../shared/types/pagination'
import CostosAdicionalesModal from './CostosAdicionalesModal.vue'
import CambiarEstadoModal from './CambiarEstadoModal.vue'
import ClienteModal from '../../OrdenServicio/components/ClienteModal.vue'
import type { Cliente } from '../../OrdenServicio/types/cliente'

// Emits
const emit = defineEmits<{
  'nueva-entrada': []
  'asignar-lote': [entrada: EntradaInventario]
}>()

// Estado
const loading = ref(false)
const entradas = ref<EntradaInventario[]>([])
const pagination = ref<Paginated<EntradaInventario>['pagination'] | null>(null)
const estadosEntrada = ref<EstadoEntrada[]>([])
const detallesAbiertos = ref(new Set<number>())

// Modal Crear Cliente
const modalCrearCliente = ref(false)

const handleClienteCreado = (cliente: Cliente) => {
  toast.success(`Cliente "${cliente.nombre}" creado exitosamente`)
  modalCrearCliente.value = false
  // Opcionalmente recargar la lista de entradas
  cargarEntradas()
}

const filtros = ref({
  estado_entrada_id: null as number | null,
  fecha_desde: null as string | null,
  fecha_hasta: null as string | null,
})

// Modal Costos
const modalCostos = ref({
  isOpen: false,
  entrada: null as EntradaInventario | null
})

// Modal Cambiar Estado
const modalCambiarEstado = ref({
  isOpen: false,
  entrada: null as EntradaInventario | null
})

// Búsqueda
const searchQuery = ref('')
const searching = ref(false)
let searchTimeout: NodeJS.Timeout | null = null

// Métodos de búsqueda
const buscar = () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
  
  searchTimeout = setTimeout(async () => {
    if (searchQuery.value.length < 2) {
      cargarEntradas()
      return
    }
    
    try {
      searching.value = true
      const { data } = await http.get('/inventario/entradas-producto/buscar', {
        params: { q: searchQuery.value }
      })
      
      entradas.value = data.data || []
      
      if (entradas.value.length === 0) {
        toast.info('No se encontraron resultados')
      }
    } catch (error) {
      console.error('Error en búsqueda:', error)
      toast.error('Error al buscar')
    } finally {
      searching.value = false
    }
  }, 300)
}

const limpiarBusqueda = () => {
  searchQuery.value = ''
  cargarEntradas()
}

// Métodos de Modal Costos
const abrirModalCostos = (entrada: EntradaInventario) => {
  modalCostos.value = {
    isOpen: true,
    entrada
  }
}

const cerrarModalCostos = () => {
  modalCostos.value = {
    isOpen: false,
    entrada: null
  }
}

const handleCostosActualizados = () => {
  cargarEntradas()
  cerrarModalCostos()
}

// Métodos de Modal Cambiar Estado
const abrirModalCambiarEstado = (entrada: EntradaInventario) => {
  modalCambiarEstado.value = {
    isOpen: true,
    entrada
  }
}

const cerrarModalCambiarEstado = () => {
  modalCambiarEstado.value = {
    isOpen: false,
    entrada: null
  }
}

const handleEstadoCambiado = () => {
  cargarEntradas()
  cerrarModalCambiarEstado()
}

// Métodos principales
const cargarEntradas = async (page = 1) => {
  loading.value = true
  try {
    const params: any = {
      page,
      per_page: 15,
      ...filtros.value,
    }

    const response = await fetchEntradasInventario(params)
    entradas.value = response.data
    pagination.value = response.pagination
  } catch (error: any) {
    console.error('Error cargando entradas:', error)
    toast.error(error?.response?.data?.message || 'Error al cargar las entradas')
  } finally {
    loading.value = false
  }
}

const cargarEstados = async () => {
  try {
    estadosEntrada.value = await fetchEstadosEntrada()
  } catch (error) {
    console.error('Error cargando estados:', error)
  }
}

const aplicarFiltros = () => {
  cargarEntradas(1)
}

const limpiarFiltros = () => {
  filtros.value = {
    estado_entrada_id: null,
    fecha_desde: null,
    fecha_hasta: null,
  }
  cargarEntradas(1)
}

const cambiarPagina = (page: number) => {
  cargarEntradas(page)
}

const toggleDetalle = (entradaId: number) => {
  if (detallesAbiertos.value.has(entradaId)) {
    detallesAbiertos.value.delete(entradaId)
  } else {
    detallesAbiertos.value.add(entradaId)
  }
}

// Helpers
const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('es-CO', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
  }).format(value)
}

const calcularTotal = (entrada: EntradaInventario) => {
  if (!entrada.items || entrada.items.length === 0) return 0
  
  return entrada.items.reduce((total, item) => {
    const costoTotal = Number(item.costo_total_item) || 0
    const costoUnitario = Number(item.costo_unitario) || 0
    const cantidad = Number(item.cantidad) || 0
    
    if (costoTotal > 0) {
      return total + costoTotal
    }
    
    if (costoUnitario > 0 && cantidad > 0) {
      return total + (costoUnitario * cantidad)
    }
    
    return total
  }, 0)
}

// Lifecycle
onMounted(() => {
  cargarEntradas()
  cargarEstados()
})

// Exponer método para recargar desde el padre
defineExpose({
  recargar: () => cargarEntradas(pagination.value?.current_page || 1)
})
</script>