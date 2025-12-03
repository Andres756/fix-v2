<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="handleClose"
    >
      <div class="bg-white rounded-lg shadow-xl w-full max-w-7xl max-h-[90vh] flex flex-col">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
          <div>
            <h2 class="text-xl font-semibold text-gray-900">
              Entradas de Inventario
            </h2>
            <p class="text-sm text-gray-500 mt-1">
              Gestiona las entradas de productos y asigna lotes
            </p>
          </div>
          <button
            @click="handleClose"
            class="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Filtros -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Proveedor/Cliente
              </label>
              <input
                v-model="filters.origen"
                type="text"
                placeholder="Buscar..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Estado
              </label>
              <select
                v-model="filters.estado_entrada_id"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option :value="null">Todos</option>
                <option
                  v-for="estado in estadosEntrada"
                  :key="estado.id"
                  :value="estado.id"
                >
                  {{ estado.nombre }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Desde
              </label>
              <input
                v-model="filters.fecha_desde"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Hasta
              </label>
              <input
                v-model="filters.fecha_hasta"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
          </div>

          <div class="mt-3 flex gap-2">
            <button
              @click="aplicarFiltros"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium"
            >
              Aplicar Filtros
            </button>
            <button
              @click="limpiarFiltros"
              class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors text-sm font-medium"
            >
              Limpiar
            </button>
          </div>
        </div>

        <!-- Lista de entradas -->
        <div class="flex-1 overflow-y-auto px-6 py-4">
          <div v-if="loading" class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
          </div>

          <div v-else-if="entradas.length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay entradas</h3>
            <p class="mt-1 text-sm text-gray-500">No se encontraron entradas con los filtros seleccionados.</p>
          </div>

          <div v-else class="space-y-4">
            <div
              v-for="entrada in entradas"
              :key="entrada.id"
              class="border border-gray-200 rounded-lg hover:shadow-md transition-shadow"
            >
              <!-- Header de la entrada -->
              <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-4">
                  <div>
                    <span class="text-sm font-medium text-gray-500">Entrada #{{ entrada.id }}</span>
                    <p class="text-xs text-gray-400 mt-0.5">
                      {{ formatDate(entrada.fecha_entrada) }}
                    </p>
                  </div>
                  
                  <div class="flex items-center gap-2">
                    <span
                      :class="getEstadoBadgeClass(entrada.estado_entrada?.codigo)"
                      class="px-2 py-1 text-xs font-medium rounded-full"
                    >
                      {{ entrada.estado_entrada?.nombre }}
                    </span>

                    <span
                      v-if="entrada.tipo_entrada === 'proveedor'"
                      class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full"
                    >
                      Proveedor
                    </span>
                    <span
                      v-else
                      class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full"
                    >
                      Cliente
                    </span>
                  </div>
                </div>

                <div class="flex items-center gap-2">
                  <button
                    v-if="!entrada.lote_id"
                    @click="abrirModalAsignarLote(entrada)"
                    class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                  >
                    Asignar Lote
                  </button>
                  <button
                    v-else
                    @click="abrirModalAsignarLote(entrada)"
                    class="px-3 py-1.5 text-sm bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
                  >
                    Editar Flete
                  </button>

                  <button
                    @click="toggleDetalle(entrada.id)"
                    class="text-gray-500 hover:text-gray-700"
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
                </div>
              </div>

              <!-- Datos principales -->
              <div class="px-4 py-3 grid grid-cols-3 gap-4 text-sm">
                <div>
                  <span class="text-gray-500">Origen:</span>
                  <p class="font-medium text-gray-900">
                    {{ entrada.proveedor?.nombre || entrada.cliente?.nombre || 'N/A' }}
                  </p>
                </div>
                <div>
                  <span class="text-gray-500">Lote:</span>
                  <p class="font-medium text-gray-900">
                    {{ entrada.lote?.numero_lote || 'Sin asignar' }}
                  </p>
                </div>
                <div>
                  <span class="text-gray-500">Motivo:</span>
                  <p class="font-medium text-gray-900">
                    {{ entrada.motivo_ingreso?.nombre || 'N/A' }}
                  </p>
                </div>
              </div>

              <!-- Detalle de items (colapsable) -->
              <div
                v-if="detallesAbiertos.has(entrada.id)"
                class="px-4 py-3 border-t border-gray-200 bg-gray-50"
              >
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Items de la entrada</h4>
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
                    <tfoot>
                      <tr class="bg-gray-50 font-semibold">
                        <td colspan="4" class="px-3 py-2 text-sm text-right text-gray-700">
                          Total Entrada:
                        </td>
                        <td class="px-3 py-2 text-sm text-right text-gray-900">
                          {{ formatCurrency(calcularTotalEntrada(entrada)) }}
                        </td>
                      </tr>
                    </tfoot>
                  </table>
                </div>

                <div v-if="entrada.observaciones" class="mt-3 text-sm">
                  <span class="text-gray-500">Observaciones:</span>
                  <p class="text-gray-700 mt-1">{{ entrada.observaciones }}</p>
                </div>
              </div>
            </div>
          </div>
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
    </div>

    <!-- Modal para asignar/editar lote -->
    <AsignarLoteModal
      v-if="modalAsignarLote.isOpen"
      :is-open="modalAsignarLote.isOpen"
      :entrada="modalAsignarLote.entrada"
      @close="cerrarModalAsignarLote"
      @success="handleLoteAsignado"
    />
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { toast } from 'vue3-toastify'
import { fetchEntradasInventario, fetchEstadosEntrada } from '../api/inventoryEntries'
import type { EntradaInventario, EstadoEntrada } from '../types/inventoryEntry'
import type { Paginated } from '../../../shared/types/pagination'
import AsignarLoteModal from '../../movimientos-inventario/components/Asignarlotemodal.vue'

// Props
interface Props {
  isOpen: boolean
}

const props = defineProps<Props>()

// Emits
const emit = defineEmits<{
  close: []
  success: []
}>()

// Estado
const loading = ref(false)
const entradas = ref<EntradaInventario[]>([])
const pagination = ref<Paginated<EntradaInventario>['pagination'] | null>(null)
const estadosEntrada = ref<EstadoEntrada[]>([])
const detallesAbiertos = ref(new Set<number>())

const filters = ref({
  origen: null as string | null,
  estado_entrada_id: null as number | null,
  fecha_desde: null as string | null,
  fecha_hasta: null as string | null,
})

const modalAsignarLote = ref<{
  isOpen: boolean
  entrada: EntradaInventario | null
}>({
  isOpen: false,
  entrada: null,
})

// Métodos
const handleClose = () => {
  emit('close')
}

const cargarEntradas = async (page = 1) => {
  loading.value = true
  try {
    const params: any = {
      page,
      per_page: 10,
      ...filters.value,
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
  filters.value = {
    origen: null,
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

const abrirModalAsignarLote = (entrada: EntradaInventario) => {
  modalAsignarLote.value = {
    isOpen: true,
    entrada,
  }
}

const cerrarModalAsignarLote = () => {
  modalAsignarLote.value = {
    isOpen: false,
    entrada: null,
  }
}

const handleLoteAsignado = () => {
  cerrarModalAsignarLote()
  cargarEntradas(pagination.value?.current_page || 1)
  toast.success('Lote asignado correctamente')
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

const getEstadoBadgeClass = (codigo?: string) => {
  if (!codigo) return 'bg-gray-100 text-gray-800'
  
  const classes: Record<string, string> = {
    pendiente: 'bg-yellow-100 text-yellow-800',
    recibido: 'bg-green-100 text-green-800',
    parcial: 'bg-blue-100 text-blue-800',
    cancelado: 'bg-red-100 text-red-800',
  }
  
  return classes[codigo] || 'bg-gray-100 text-gray-800'
}

const calcularTotalEntrada = (entrada: EntradaInventario) => {
  if (!entrada.items || entrada.items.length === 0) return 0
  
  return entrada.items.reduce((total, item) => {
    return total + (item.costo_total_item || (item.costo_unitario * item.cantidad))
  }, 0)
}

// Lifecycle
onMounted(() => {
  cargarEntradas()
  cargarEstados()
})

watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    cargarEntradas()
  }
})
</script>