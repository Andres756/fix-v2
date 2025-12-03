<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <!-- Header con botón Nueva Entrada -->
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
      <div>
        <h2 class="text-lg font-semibold text-gray-900">Entradas de Inventario</h2>
        <p class="text-sm text-gray-500 mt-1">Gestiona las entradas de productos al inventario</p>
      </div>
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
          <tr v-for="entrada in entradas" :key="entrada.id" class="hover:bg-gray-50">
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
                :class="getEstadoBadgeClass(entrada.estado_entrada?.codigo)"
                class="px-2 py-1 text-xs font-medium rounded-full"
              >
                {{ entrada.estado_entrada?.nombre }}
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
              </div>
            </td>
          </tr>

          <!-- Fila expandible con detalles -->
          <tr v-if="detallesAbiertos.has(entrada.id)" :key="`detalle-${entrada.id}`">
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
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { toast } from 'vue3-toastify'
import { fetchEntradasInventario, fetchEstadosEntrada } from '../../inventario/api/inventoryEntries'
import type { EntradaInventario, EstadoEntrada } from '../../inventario/types/inventoryEntry'
import type { Paginated } from '../types/pagination'

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

const filtros = ref({
  estado_entrada_id: null as number | null,
  fecha_desde: null as string | null,
  fecha_hasta: null as string | null,
})

// Métodos
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

const calcularTotal = (entrada: EntradaInventario) => {
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

// Exponer método para recargar desde el padre
defineExpose({
  recargar: () => cargarEntradas(pagination.value?.current_page || 1)
})
</script>