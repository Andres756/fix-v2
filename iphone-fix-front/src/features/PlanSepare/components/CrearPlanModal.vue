<template>
  <Teleport to="body">
    <transition name="modal">
      <div
        v-if="open"
        class="fixed inset-0 z-[9999] overflow-y-auto"
        @click.self="emit('close')"
      >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
          <!-- Overlay -->
          <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-50 backdrop-blur-sm" @click="emit('close')"></div>

          <!-- Modal -->
          <div class="relative inline-block w-full max-w-2xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-xl shadow-2xl z-[10000]">
            <!-- Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                  </div>
                  <h3 class="text-xl font-bold text-white">Crear Plan Separe</h3>
                </div>
                <button
                  @click="emit('close')"
                  class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-1 transition-colors"
                >
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Body -->
            <form @submit.prevent="handleSubmit" class="p-6">
              <div class="space-y-6">
                <!-- Buscar Cliente -->
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Cliente *
                  </label>
                  <div class="relative">
                    <input
                      v-model="searchCliente"
                      @input="buscarClientes"
                      type="text"
                      placeholder="Buscar por nombre o documento..."
                      class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                      :class="{ 'border-red-300': clienteSeleccionado && !clienteSeleccionado.id }"
                    />
                    <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>

                    <!-- Resultados de búsqueda -->
                    <ul
                      v-if="clientesFiltrados.length > 0 && searchCliente"
                      class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto"
                    >
                      <li
                        v-for="cliente in clientesFiltrados"
                        :key="cliente.id"
                        @click="seleccionarCliente(cliente)"
                        class="px-4 py-3 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors"
                      >
                        <div class="font-medium text-gray-900">{{ cliente.nombre }}</div>
                        <div class="text-sm text-gray-500">{{ cliente.documento }}</div>
                      </li>
                    </ul>
                  </div>
                  
                  <!-- Cliente seleccionado -->
                  <div v-if="clienteSeleccionado?.id" class="mt-2 p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                    <div class="flex items-center justify-between">
                      <div>
                        <p class="text-sm font-medium text-indigo-900">{{ clienteSeleccionado.nombre }}</p>
                        <p class="text-xs text-indigo-600">{{ clienteSeleccionado.documento }}</p>
                      </div>
                      <button
                        type="button"
                        @click="limpiarCliente"
                        class="text-indigo-600 hover:text-indigo-800"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Buscar Inventario -->
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Producto / Equipo *
                  </label>
                  <div class="relative">
                    <input
                      v-model="searchInventario"
                      @input="buscarInventarios"
                      type="text"
                      placeholder="Buscar por código o nombre..."
                      class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                      :class="{ 'border-red-300': inventarioSeleccionado && !inventarioSeleccionado.id }"
                    />
                    <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>

                    <!-- Resultados de búsqueda -->
                    <ul
                      v-if="inventariosFiltrados.length > 0 && searchInventario"
                      class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto"
                    >
                      <li
                        v-for="inv in inventariosFiltrados"
                        :key="inv.id"
                        @click="seleccionarInventario(inv)"
                        class="px-4 py-3 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors"
                      >
                        <div class="flex items-center justify-between">
                          <div>
                            <div class="font-medium text-gray-900">{{ inv.nombre }}</div>
                            <div class="text-sm text-gray-500">Código: {{ inv.codigo }}</div>
                          </div>
                          <div class="text-right">
                            <div class="text-sm font-semibold text-green-600">
                              ${{ Number(inv.precio_venta || 0).toLocaleString('es-CO') }}
                            </div>
                            <div class="text-xs text-gray-500">Stock: {{ inv.stock_disponible }}</div>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>
                  
                  <!-- Inventario seleccionado -->
                  <div v-if="inventarioSeleccionado?.id" class="mt-2 p-3 bg-green-50 rounded-lg border border-green-200">
                    <div class="flex items-center justify-between">
                      <div class="flex-1">
                        <p class="text-sm font-medium text-green-900">{{ inventarioSeleccionado.nombre }}</p>
                        <p class="text-xs text-green-600">{{ inventarioSeleccionado.codigo }}</p>
                      </div>
                      <div class="text-right mr-3">
                        <p class="text-lg font-bold text-green-700">
                          ${{ Number(inventarioSeleccionado.precio_venta || 0).toLocaleString('es-CO') }}
                        </p>
                      </div>
                      <button
                        type="button"
                        @click="limpiarInventario"
                        class="text-green-600 hover:text-green-800"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Precio y Porcentaje -->
                <div class="grid grid-cols-2 gap-4">
                  <!-- Precio Total -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                      Precio Total *
                    </label>
                    <div class="relative">
                      <span class="absolute left-3 top-2.5 text-gray-500">$</span>
                      <input
                        v-model.number="form.precio_total"
                        type="number"
                        min="0"
                        step="1000"
                        required
                        class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                      />
                    </div>
                  </div>

                  <!-- Porcentaje Mínimo -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                      % Mínimo para Asegurar *
                    </label>
                    <div class="relative">
                      <input
                        v-model.number="form.porcentaje_minimo"
                        type="number"
                        min="10"
                        max="100"
                        required
                        class="w-full pr-8 pl-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                      />
                      <span class="absolute right-3 top-2.5 text-gray-500">%</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                      Mínimo: ${{ montoMinimo.toLocaleString('es-CO') }}
                    </p>
                  </div>
                </div>

                <!-- Observaciones -->
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Observaciones
                  </label>
                  <textarea
                    v-model="form.observaciones"
                    rows="3"
                    placeholder="Notas adicionales sobre el plan separe..."
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"
                  ></textarea>
                </div>

                <!-- Resumen -->
                <div class="p-4 bg-gradient-to-r from-indigo-50 to-blue-50 rounded-lg border border-indigo-200">
                  <h4 class="text-sm font-semibold text-indigo-900 mb-3">📊 Resumen del Plan</h4>
                  <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="flex justify-between">
                      <span class="text-gray-600">Precio total:</span>
                      <span class="font-semibold text-gray-900">${{ form.precio_total.toLocaleString('es-CO') }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">% Mínimo:</span>
                      <span class="font-semibold text-indigo-600">{{ form.porcentaje_minimo }}%</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Abono mínimo:</span>
                      <span class="font-semibold text-green-600">${{ montoMinimo.toLocaleString('es-CO') }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Saldo restante:</span>
                      <span class="font-semibold text-orange-600">${{ (form.precio_total - montoMinimo).toLocaleString('es-CO') }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Footer -->
              <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                <button
                  type="button"
                  @click="emit('close')"
                  class="px-6 py-2.5 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 font-medium transition-colors"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  :disabled="isSubmitting || !clienteSeleccionado?.id || !inventarioSeleccionado?.id"
                  class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
                >
                  <svg v-if="isSubmitting" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span>{{ isSubmitting ? 'Creando...' : 'Crear Plan Separe' }}</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { toast } from 'vue3-toastify'
import type { Cliente } from '../../Facturacion/types/factura'
import type { CreatePlanSeparePayload, PlanSepare } from '../types/planSepare'
import { createPlanSepare } from '../api/planSepare'
import { fetchClientes } from '../../OrdenServicio/api/clientes'
import { fetchInventario } from '../../inventario/api/inventario'

const props = defineProps<{
  open: boolean
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'created', plan: PlanSepare): void
}>()

// Estado de búsqueda de cliente
const searchCliente = ref('')
const clientesFiltrados = ref<Cliente[]>([])
const clienteSeleccionado = ref<Cliente | null>(null)

// Estado de búsqueda de inventario
const searchInventario = ref('')
const inventariosFiltrados = ref<any[]>([])
const inventarioSeleccionado = ref<any>(null)

// Estado del formulario
const isSubmitting = ref(false)
const form = ref<CreatePlanSeparePayload>({
  cliente_id: 0,
  inventario_id: 0,
  precio_total: 0,
  porcentaje_minimo: 30,
  observaciones: ''
})

// Monto mínimo calculado
const montoMinimo = computed(() => {
  return (form.value.precio_total * form.value.porcentaje_minimo) / 100
})

// Buscar clientes
async function buscarClientes() {
  if (searchCliente.value.length < 2) {
    clientesFiltrados.value = []
    return
  }
  
  try {
    const response = await fetchClientes({ q: searchCliente.value, per_page: 10 })
    clientesFiltrados.value = response.data
  } catch (error) {
    console.error('Error buscando clientes:', error)
    toast.error('Error al buscar clientes')
  }
}

function seleccionarCliente(cliente: Cliente) {
  clienteSeleccionado.value = cliente
  searchCliente.value = `${cliente.nombre} - ${cliente.documento}`
  clientesFiltrados.value = []
  form.value.cliente_id = cliente.id
}

function limpiarCliente() {
  clienteSeleccionado.value = null
  searchCliente.value = ''
  form.value.cliente_id = 0
}

// Buscar inventarios
async function buscarInventarios() {
  if (searchInventario.value.length < 2) {
    inventariosFiltrados.value = []
    return
  }
  
  try {
    const response = await fetchInventario({ 
      q: searchInventario.value, 
      per_page: 10,
      con_stock: true
    })
    inventariosFiltrados.value = response.data
  } catch (error) {
    console.error('Error buscando inventarios:', error)
    toast.error('Error al buscar productos')
  }
}

function seleccionarInventario(inv: any) {
  inventarioSeleccionado.value = inv
  searchInventario.value = `${inv.codigo} - ${inv.nombre}`
  inventariosFiltrados.value = []
  form.value.inventario_id = inv.id
  form.value.precio_total = Number(inv.precio_venta || 0)
}

function limpiarInventario() {
  inventarioSeleccionado.value = null
  searchInventario.value = ''
  form.value.inventario_id = 0
  form.value.precio_total = 0
}

// Enviar formulario
async function handleSubmit() {
  if (!form.value.cliente_id || !form.value.inventario_id) {
    toast.warning('Debe seleccionar un cliente y un producto')
    return
  }

  if (form.value.precio_total <= 0) {
    toast.warning('El precio debe ser mayor a cero')
    return
  }

  if (form.value.porcentaje_minimo < 10 || form.value.porcentaje_minimo > 100) {
    toast.warning('El porcentaje debe estar entre 10% y 100%')
    return
  }

  isSubmitting.value = true

  try {
    const plan = await createPlanSepare(form.value)
    toast.success('¡Plan Separe creado exitosamente!')
    emit('created', plan)
    resetForm()
    emit('close')
  } catch (error: any) {
    console.error('Error al crear plan:', error)

    // 🔹 Captura el mensaje del backend
    const msg =
      error?.message ||
      error?.response?.data?.error ||
      error?.response?.data?.message ||
      'Error al crear el plan separe.'

    toast.error(msg)
  } finally {
    isSubmitting.value = false
  }
}

function resetForm() {
  form.value = {
    cliente_id: 0,
    inventario_id: 0,
    precio_total: 0,
    porcentaje_minimo: 30,
    observaciones: ''
  }
  
  clienteSeleccionado.value = null
  inventarioSeleccionado.value = null
  searchCliente.value = ''
  searchInventario.value = ''
}

// Reset cuando se cierra el modal
watch(() => props.open, (newVal) => {
  if (!newVal) {
    resetForm()
  }
})
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>