<!-- features/Facturacion/components/NuevaFacturaModal.vue -->
<template>
  <Teleport to="body">
    <transition
      name="modal"
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
      appear
    >
      <div v-if="open" class="fixed inset-0 z-[9999] overflow-y-auto">
        <!-- Backdrop -->
        <div
          class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
          @click="handleClose"
        ></div>

        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
          <div class="relative w-full max-w-6xl bg-white rounded-2xl shadow-2xl">
            <!-- Header con tabs -->
            <div class="border-b border-gray-200">
              <div class="flex items-center justify-between p-6 pb-0">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-xl font-bold text-gray-900">Nueva Factura</h3>
                    <p class="text-sm text-gray-500">Registra una venta o servicio</p>
                  </div>
                </div>
                <button
                  @click="handleClose"
                  :disabled="isSaving"
                  class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <!-- Tabs -->
              <div class="flex gap-1 px-6 mt-4">
                <button
                  @click="tipoFactura = 'venta'"
                  :class="[
                    'px-4 py-2 font-medium rounded-t-lg transition-colors',
                    tipoFactura === 'venta' 
                      ? 'bg-white text-blue-600 border-t border-l border-r border-gray-200' 
                      : 'text-gray-500 hover:text-gray-700'
                  ]"
                >
                  Venta Directa
                </button>
                <button
                  @click="tipoFactura = 'servicio'"
                  :class="[
                    'px-4 py-2 font-medium rounded-t-lg transition-colors',
                    tipoFactura === 'servicio' 
                      ? 'bg-white text-blue-600 border-t border-l border-r border-gray-200' 
                      : 'text-gray-500 hover:text-gray-700'
                  ]"
                >
                  Facturar Servicio
                </button>
              </div>
            </div>

            <!-- Formulario de Venta Directa -->
            <form v-if="tipoFactura === 'venta'" @submit.prevent="handleSubmitVenta" class="p-6">
              <div class="space-y-6">
              <!-- Cliente buscable -->
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">
                  Cliente *
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                      />
                    </svg>
                  </div>
                  <input
                    v-model="searchCliente"
                    @input="buscarClientes"
                    required
                    type="text"
                    placeholder="Buscar por documento o apellido..."
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  />

                  <!-- Resultados -->
                  <ul
                    v-if="clientesFiltrados.length > 0 && searchCliente"
                    class="absolute bg-white border border-gray-200 rounded-lg mt-1 w-full max-h-40 overflow-y-auto shadow-lg z-10"
                  >
                    <li
                      v-for="c in clientesFiltrados"
                      :key="c.id"
                      @click="seleccionarCliente(c)"
                      class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors"
                    >
                      <div class="font-medium text-gray-900">{{ c.nombre }}</div>
                      <div class="text-sm text-gray-500">{{ c.documento }}</div>
                      <div v-if="c.telefono" class="text-xs text-gray-400">{{ c.telefono }}</div>
                    </li>
                  </ul>
                </div>
              </div>

                <!-- Productos -->
                <div class="space-y-4">
                  <div class="flex items-end gap-3">
                    <!-- Buscador -->
                    <div class="flex-1 relative">
                      <label class="block text-sm font-semibold text-gray-700 mb-1">Producto *</label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                          </svg>
                        </div>
                        <input
                          v-model="searchProducto"
                          @input="buscarProductos"
                          type="text"
                          placeholder="Buscar por nombre o código..."
                          class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        />
                        <!-- Resultados -->
                        <ul
                          v-if="productosFiltrados.length > 0 && searchProducto"
                          class="absolute left-0 right-0 bg-white border border-gray-200 rounded-lg mt-1 w-full max-h-56 overflow-y-auto shadow-lg z-20"
                        >
                          <li
                            v-for="p in productosFiltrados"
                            :key="p.id"
                            @click="seleccionarProducto(p)"
                            class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors"
                          >
                            <div class="font-medium text-gray-900">{{ p.nombre }}</div>
                            <div class="text-sm text-gray-500">{{ p.codigo }}</div>
                            <div class="text-xs text-gray-400">$ {{ formatMoney(p.precio || 0) }}</div>
                          </li>
                        </ul>
                      </div>
                    </div>

                    <!-- Cantidad -->
                    <div class="w-24">
                      <label class="block text-sm font-semibold text-gray-700 mb-1">Cant.</label>
                      <input
                        v-model.number="cantidadTemp"
                        type="number"
                        min="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      />
                    </div>

                    <!-- Tipo precio -->
                    <div class="w-32">
                      <label class="block text-sm font-semibold text-gray-700 mb-1">Tipo</label>
                      <select
                        v-model="tipoPrecioTemp"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      >
                        <option value="DET">Detal</option>
                        <option value="MAY">Mayorista</option>
                      </select>
                    </div>

                    <!-- Botón -->
                    <div>
                      <button
                        type="button"
                        @click="agregarProducto"
                        class="mt-6 px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                      >
                        Agregar
                      </button>
                    </div>
                  </div>

                  <!-- Tabla de productos agregados -->
                  <div v-if="ventaForm.items.length > 0" class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                      <thead class="bg-gray-50 text-gray-700">
                        <tr>
                          <th class="px-4 py-2 text-left">Código</th>
                          <th class="px-4 py-2 text-left">Nombre</th>
                          <th class="px-4 py-2 text-center">Cant.</th>
                          <th class="px-4 py-2 text-center">Precio</th>
                          <th class="px-4 py-2 text-center">Subtotal</th>
                          <th class="px-4 py-2 text-center">Acciones</th>
                        </tr>
                      </thead>
                      <tbody class="divide-y divide-gray-100 bg-white">
                        <tr v-for="(item, index) in ventaForm.items" :key="index">
                          <td class="px-4 py-2">{{ item.codigo }}</td>
                          <td class="px-4 py-2">{{ item.nombre }}</td>
                          <td class="px-4 py-2 text-center">{{ item.cantidad }}</td>
                          <td class="px-4 py-2 text-center">{{ formatMoney(item.precio) }}</td>
                          <td class="px-4 py-2 text-center font-semibold">
                            {{ formatMoney(item.cantidad * item.precio) }}
                          </td>
                          <td class="px-4 py-2 text-center">
                            <button
                              @click="removeProducto(index)"
                              class="p-2 text-red-600 hover:bg-red-50 rounded-full transition-colors flex items-center justify-center mx-auto"
                              title="Eliminar producto"
                            >
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-5 h-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                              >
                                <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                />
                              </svg>
                            </button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- Forma de pago y observaciones -->
                <div class="grid grid-cols-2 gap-4">
                  <!-- Forma de pago -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Forma de Pago</label>
                    <select
                      v-model="ventaForm.forma_pago_id"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                      <option value="">Seleccione (opcional)</option>
                      <option
                        v-for="forma in formasPago"
                        :key="forma.id"
                        :value="forma.id"
                      >
                        {{ forma.nombre }}
                      </option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                    <input
                      v-model="ventaForm.observaciones"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Notas adicionales (opcional)"
                    />
                  </div>
                </div>

                <!-- Control de entrega -->
                <div class="flex items-center gap-3">
                  <input
                    v-model="ventaForm.entregado"
                    type="checkbox"
                    id="entregado"
                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                  />
                  <label for="entregado" class="text-sm text-gray-700">
                    Marcar como entregado
                  </label>
                </div>

                <!-- Total -->
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                  <div class="flex justify-between items-center">
                    <span class="text-lg font-medium text-gray-700">Total a Facturar:</span>
                    <span class="text-2xl font-bold text-gray-900">{{ formatMoney(calcularTotalVenta()) }}</span>
                  </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end gap-3">
                  <button
                    type="button"
                    @click="handleClose"
                    :disabled="isSaving"
                    class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                  >
                    Cancelar
                  </button>
                  <button
                    type="submit"
                    :disabled="isSaving || ventaForm.items.length === 0"
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium disabled:bg-blue-300 disabled:cursor-not-allowed flex items-center gap-2"
                  >
                    <svg v-if="isSaving" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ isSaving ? 'Creando...' : 'Crear Factura' }}
                  </button>
                </div>
              </div>
            </form>

            <!-- Formulario de Facturar Servicio -->
            <form v-else-if="tipoFactura === 'servicio'" @submit.prevent="handleSubmitServicio" class="p-6">
              <div class="space-y-6">
              <!-- Seleccionar cliente -->
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Cliente *</label>
                <div class="relative">
                  <input
                    v-model="searchClienteServicio"
                    @input="buscarClientesServicio"
                    type="text"
                    placeholder="Buscar cliente..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                  />

                  <!-- Resultados -->
                  <ul
                    v-if="clientesServicioFiltrados.length > 0 && searchClienteServicio"
                    class="absolute z-10 bg-white border border-gray-200 rounded-lg mt-1 w-full max-h-48 overflow-y-auto shadow-lg"
                  >
                    <li
                      v-for="c in clientesServicioFiltrados"
                      :key="c.id"
                      @click="seleccionarClienteServicio(c)"
                      class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors"
                    >
                      <div class="font-medium text-gray-900">{{ c.nombre }}</div>
                      <div class="text-sm text-gray-500">{{ c.documento }}</div>
                    </li>
                  </ul>
                </div>
              </div>

              <!-- Órdenes del cliente -->
              <div v-if="ordenesCliente.length > 0" class="mt-4">
                <label class="block text-sm font-semibold text-gray-700">Órdenes de servicio</label>
                <select
                  v-model="ordenSeleccionadaId"
                  @change="seleccionarOrden(Number(ordenSeleccionadaId))"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Seleccione una orden</option>
                  <option
                    v-for="orden in ordenesCliente"
                    :key="orden.id"
                    :value="orden.id"
                  >
                    {{ orden.codigo_orden }} - {{ orden.estado.charAt(0).toUpperCase() + orden.estado.slice(1) }}
                  </option>
                </select>
              </div>

              <!-- Equipos asociados -->
              <div v-if="equiposOrdenSeleccionada.length > 0" class="mt-4 border rounded-lg overflow-hidden">
              <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-700">
                  <tr>
                    <th class="pl-4 py-2 text-left w-12">Facturar</th>
                    <th class="px-4 py-2 text-left">Equipo</th>
                    <th class="px-4 py-2 text-left">Estado</th>
                    <th class="px-4 py-2 text-center w-24">Entregado</th>
                    <th class="px-4 py-2 text-center w-24">Facturado</th>
                    <th class="px-4 py-2 text-right w-28">Total</th>
                  </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                  <tr v-for="equipo in equiposOrdenSeleccionada" :key="equipo.id">
                    <!-- Checkbox de Facturar -->
                    <td class="pl-4 py-2 text-left">
                      <input
                        type="checkbox"
                        :value="equipo.id"
                        v-model="servicioForm.equipos_seleccionados"
                        :checked="equipo.seleccionado"
                        :disabled="equipo.bloqueado"
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 disabled:opacity-60"
                      />
                    </td>

                    <!-- Datos del Equipo -->
                    <td class="px-4 py-2">
                      {{ equipo.modelo || equipo.descripcion }}
                      <div class="text-xs text-gray-500">
                        IMEI: {{ equipo.imei_serial || '—' }}
                      </div>
                    </td>

                    <!-- Estado -->
                    <td class="px-4 py-2 capitalize">
                      {{ equipo._estadoPlano || 'pendiente' }}
                    </td>

                    <!-- Entregado -->
                    <td class="px-4 py-2 text-center">
                      <input
                        type="checkbox"
                        :checked="Number(equipo.entregado) === 1"
                        disabled
                        class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500 disabled:opacity-70"
                      />
                    </td>

                    <!-- Facturado -->
                    <td class="px-4 py-2 text-center">
                      <input
                        type="checkbox"
                        :checked="Number(equipo.facturado) === 1"
                        disabled
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 disabled:opacity-70"
                      />
                    </td>

                    <!-- Total -->
                    <td class="px-4 py-2 text-right font-medium text-gray-900">
                      {{ formatMoney(equipo.precio_total || 0) }}
                    </td>
                  </tr>
                </tbody>
              </table>
              </div>

                <!-- Forma de pago y observaciones -->
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Forma de Pago</label>
                    <select
                      v-model="servicioForm.forma_pago_id"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                      <option value="">Seleccione (opcional)</option>
                      <option v-for="forma in formasPago" :key="forma.id" :value="forma.id">
                        {{ forma.nombre }}
                      </option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                    <input
                      v-model="servicioForm.observaciones"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Notas adicionales (opcional)"
                    />
                  </div>
                </div>

                <!-- Control de entrega -->
                <div class="flex items-center gap-3">
                  <input
                    v-model="servicioForm.entregado"
                    type="checkbox"
                    id="entregado-servicio"
                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                  />
                  <label for="entregado-servicio" class="text-sm text-gray-700">
                    Marcar como entregado
                  </label>
                </div>

                <!-- Total -->
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                  <div class="flex justify-between items-center">
                    <span class="text-lg font-medium text-gray-700">Total a Facturar:</span>
                    <span class="text-2xl font-bold text-gray-900">
                      {{ formatMoney(calcularTotalServicio()) }}
                    </span>
                  </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end gap-3">
                  <button
                    type="button"
                    @click="handleClose"
                    :disabled="isSaving"
                    class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                  >
                    Cancelar
                  </button>
                  <button
                    type="submit"
                    :disabled="isSaving || !servicioForm.orden_servicio_id || servicioForm.equipos_seleccionados.length === 0"
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium disabled:bg-blue-300 disabled:cursor-not-allowed flex items-center gap-2"
                  >
                    <svg v-if="isSaving" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ isSaving ? 'Creando...' : 'Facturar Servicio' }}
                  </button>
                </div>
              </div>
            </form>

          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { toast } from 'vue3-toastify'
import { 
  createFacturaVenta, 
  createFacturaServicio,
} from '../api/facturacion'

// Estado búsqueda cliente (búsqueda en vivo)
import { fetchFormasPago,  fetchOrdenesPorCliente, fetchEquiposDeOrden} from '../api/facturacion'
import { fetchClientes  } from '../../OrdenServicio/api/clientes' // ✅ asegúrate que existe este endpoint
import { fetchInventario } from '../../inventario/api/inventario'

// cliente
const searchCliente = ref('')
const clientesFiltrados = ref<any[]>([])
const selectedClienteId = ref<number | ''>('')

// Productos
const searchProducto = ref('')
const productosFiltrados = ref<any[]>([])
const productoSeleccionado = ref<any | null>(null)
const cantidadTemp = ref(1)
const tipoPrecioTemp = ref<'DET' | 'MAY'>('DET')

// Lista de formas de pago
const formasPago = ref<any[]>([])

// Estado de búsqueda de cliente (para facturar servicio)
const searchClienteServicio = ref('')
const clientesServicioFiltrados = ref<any[]>([])
const clienteSeleccionadoServicio = ref<any | null>(null)

// Órdenes y equipos
const ordenesCliente = ref<any[]>([])
const ordenSeleccionadaId = ref<number | null>(null)
const equiposOrdenSeleccionada = ref<any[]>([])

// Buscar clientes en vivo
async function buscarClientes() {
  if (searchCliente.value.length < 2) {
    clientesFiltrados.value = []
    return
  }
  try {
    const res = await fetchClientes({ q: searchCliente.value, per_page: 5 })
    clientesFiltrados.value = res.data
  } catch (error) {
    console.error('Error buscando clientes:', error)
    clientesFiltrados.value = []
  }
}

// Seleccionar cliente
function seleccionarCliente(cliente: any) {
  selectedClienteId.value = cliente.id
  searchCliente.value = `${cliente.nombre} - ${cliente.documento}`
  clientesFiltrados.value = []
  ventaForm.cliente_id = cliente.id // 🔗 conecta con el formulario de venta
}

// Buscar productos en vivo
async function buscarProductos() {
  if (searchProducto.value.length < 2) {
    productosFiltrados.value = []
    return
  }
  try {
    const res = await fetchInventario({ q: searchProducto.value, per_page: 5 })
    productosFiltrados.value = res.data || []
  } catch {
    productosFiltrados.value = []
  }
}

// Seleccionar producto
function seleccionarProducto(producto: any) {
  productoSeleccionado.value = producto
  searchProducto.value = `${producto.nombre} - ${producto.codigo}`
  productosFiltrados.value = []

  // Guarda el precio base del producto seleccionado
  productoSeleccionado.value.precio_unitario =
    tipoPrecioTemp.value === 'MAY'
      ? producto.costo_mayor || producto.precio || 0
      : producto.precio || producto.costo_mayor || 0
}

// Agregar producto a la lista
function agregarProducto() {
  if (!productoSeleccionado.value) {
    toast.warning('Debes seleccionar un producto antes de agregarlo')
    return
  }

  const producto = productoSeleccionado.value

  // Determinar precio según tipo de venta
  const precioSeleccionado =
    tipoPrecioTemp.value === 'MAY'
      ? producto.costo_mayor || producto.precio || 0
      : producto.precio || producto.costo_mayor || 0

  // Agregar a la lista
  ventaForm.items.push({
    inventario_id: producto.id,
    codigo: producto.codigo,
    nombre: producto.nombre,
    cantidad: cantidadTemp.value,
    tipo_precio: tipoPrecioTemp.value,
    precio: precioSeleccionado,
  })

  // Reset campos temporales
  productoSeleccionado.value = null
  searchProducto.value = ''
  cantidadTemp.value = 1
}

// Cargar al montar
onMounted(async () => {
  try {
    const res = await fetchFormasPago()
    // Asegúrate que el endpoint devuelva un array con { id, nombre }
    formasPago.value = res.data || res
  } catch (error) {
    console.error('Error cargando formas de pago:', error)
    formasPago.value = []
  }
})

// Buscar clientes en vivo
async function buscarClientesServicio() {
  if (searchClienteServicio.value.length < 2) {
    clientesServicioFiltrados.value = []
    return
  }

  try {
    const res = await fetchClientes({ q: searchClienteServicio.value, per_page: 5 })
    clientesServicioFiltrados.value = res.data || []
  } catch (error) {
    console.error('Error buscando clientes:', error)
    toast.error('No se pudo buscar el cliente.')
  }
}

// Seleccionar cliente
function seleccionarClienteServicio(cliente: any) {
  clienteSeleccionadoServicio.value = cliente
  searchClienteServicio.value = `${cliente.nombre} - ${cliente.documento}`
  clientesServicioFiltrados.value = []
  cargarOrdenesCliente()
}

// cargar ordenes
async function cargarOrdenesCliente() {
  if (!clienteSeleccionadoServicio.value?.id) return

  try {
    const resp = await fetchOrdenesPorCliente(clienteSeleccionadoServicio.value.id)
    const ordenes = resp.data ? resp.data : resp

    // ✅ Filtrar solo las pendientes
    ordenesCliente.value = ordenes.filter(o => o.estado === 'pendiente')

    console.log('📦 Órdenes pendientes:', ordenesCliente.value)
  } catch (error) {
    console.error('Error cargando órdenes:', error)
    toast.error('No se pudieron cargar las órdenes del cliente.')
    ordenesCliente.value = []
  }
}

// Seleccionar orden
async function seleccionarOrden(ordenId: number) {
  console.log('🟢 seleccionando orden:', ordenId)
  ordenSeleccionadaId.value = ordenId

  // Buscar la orden dentro del array
  const orden = ordenesCliente.value.find(o => o.id === ordenId)
  if (!orden) {
    console.warn('⚠️ No se encontró la orden seleccionada.')
    equiposOrdenSeleccionada.value = []
    servicioForm.value.equipos_seleccionados = []
    return
  }

  // Mostrar datos recibidos
  console.log('📦 Equipos crudos de la orden:', orden.equipos)

  // Procesar los equipos
  equiposOrdenSeleccionada.value = (orden.equipos || []).map(eq => {
    const estadoPlano = (
      eq?.estado?.codigo ||
      eq?.estado?.nombre ||
      eq?.estado ||
      ''
    ).toString().toLowerCase()

    const esFinalizado = estadoPlano.includes('finalizado')
    const estaFacturado = Number(eq.facturado) === 1
    const estaEntregado = Number(eq.entregado) === 1

    const seleccionado = esFinalizado && !estaFacturado
    const bloqueado = !esFinalizado || estaFacturado

    console.log(
      `🧩 Equipo ${eq.id}: estado=${estadoPlano}, facturado=${eq.facturado}, entregado=${eq.entregado}, seleccionado=${seleccionado}, bloqueado=${bloqueado}`
    )

    return {
      ...eq,
      _estadoPlano: estadoPlano,
      total: eq.precio_total || 0,
      seleccionado,
      bloqueado,
    }
  })

  // Inicializar equipos seleccionados
  servicioForm.value.equipos_seleccionados = equiposOrdenSeleccionada.value
    .filter(e => e.seleccionado)
    .map(e => e.id)

  console.log('✅ Equipos procesados:', equiposOrdenSeleccionada.value)
  console.log('🧾 IDs seleccionados por defecto:', servicioForm.value.equipos_seleccionados)
}

// Helper para formatear valores monetarios
function formatMoney(amount: number): string {
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(amount || 0)
}


// Importar APIs necesarias (asumiendo que existen)
// import { fetchClientes } from '../../Cliente/api/cliente'
// import { fetchInventario } from '../../inventario/api/inventario'
// import { fetchOrdenesFacturables } from '../../OrdenServicio/api/orden'

// Props
interface Props {
  open: boolean
}

const props = defineProps<Props>()

// Emits
const emit = defineEmits<{
  (e: 'close'): void
  (e: 'success'): void
}>()

// Estado general
const isSaving = ref(false)
const tipoFactura = ref<'venta' | 'servicio' | 'prefactura'>('venta')

// Catálogos
const productos = ref<any[]>([])
const ordenesDisponibles = ref<any[]>([])
const equiposOrden = ref<any[]>([])

// Formulario Venta Directa
const ventaForm = reactive({
  cliente_id: '',
  forma_pago_id: '',
  observaciones: '',
  entregado: true,
  items: [
    {
      inventario_id: '',
      cantidad: 1,
      tipo_precio: 'DET' as 'DET' | 'MAY',
      precio: 0
    }
  ]
})

// Formulario Servicio
const servicioForm = reactive({
  orden_servicio_id: '',
  forma_pago_id: '',
  observaciones: '',
  entregado: true,
  equipos_seleccionados: [] as number[]
})

// Métodos para productos
const addProducto = () => {
  ventaForm.items.push({
    inventario_id: '',
    cantidad: 1,
    tipo_precio: 'DET',
    precio: 0
  })
}

const removeProducto = (index: number) => {
  ventaForm.items.splice(index, 1)
}

const onProductoChange = (index: number) => {
  const item = ventaForm.items[index]
  const producto = productos.value.find(p => p.id === Number(item.inventario_id))
  
  if (producto) {
    item.precio = item.tipo_precio === 'MAY' && producto.costo_mayor
      ? producto.costo_mayor
      : producto.precio
  }
}

const onTipoPrecioChange = (index: number) => {
  onProductoChange(index)
}

const calculateItemTotal = (index: number) => {
  // El total se recalcula automáticamente con computed
}

// Cálculo de totales
const calcularTotalVenta = () => {
  return ventaForm.items.reduce((sum, item) => {
    return sum + (item.cantidad * (item.precio || 0))
  }, 0)
}

const calcularTotalServicio = () => {
  return equiposOrden.value
    .filter(e => servicioForm.equipos_seleccionados.includes(e.id))
    .reduce((sum, equipo) => sum + (equipo.total || 0), 0)
}

// Enviar formulario de venta
const handleSubmitVenta = async () => {
  if (!selectedClienteId.value) {
    toast.warning('Debe seleccionar un cliente')
    return
  }
  ventaForm.cliente_id = selectedClienteId.value.toString()
  
  const itemsValidos = ventaForm.items.filter(item => item.inventario_id && item.cantidad > 0)
  if (itemsValidos.length === 0) {
    toast.warning('Debe agregar al menos un producto')
    return
  }
  
  try {
    isSaving.value = true
    
    const payload = {
      origen: 'venta' as const,
      cliente_id: Number(ventaForm.cliente_id),
      forma_pago_id: ventaForm.forma_pago_id ? Number(ventaForm.forma_pago_id) : undefined,
      observaciones: ventaForm.observaciones || undefined,
      entregado: ventaForm.entregado,
      items: itemsValidos.map(item => ({
        inventario_id: Number(item.inventario_id),
        cantidad: item.cantidad,
        tipo_precio: item.tipo_precio
      }))
    }
    
    await createFacturaVenta(payload)
    toast.success('Factura creada exitosamente')
    emit('success')
    handleClose()
  } catch (error: any) {
    console.error('Error creando factura:', error)
    toast.error(error.message || 'Error al crear la factura')
  } finally {
    isSaving.value = false
  }
}

// Enviar formulario de servicio
const handleSubmitServicio = async () => {
  if (!servicioForm.orden_servicio_id) {
    toast.warning('Debe seleccionar una orden de servicio')
    return
  }
  
  if (servicioForm.equipos_seleccionados.length === 0) {
    toast.warning('Debe seleccionar al menos un equipo')
    return
  }
  
  try {
    isSaving.value = true
    
    const payload = {
      origen: 'servicio' as const,
      orden_servicio_id: Number(servicioForm.orden_servicio_id),
      forma_pago_id: servicioForm.forma_pago_id ? Number(servicioForm.forma_pago_id) : undefined,
      observaciones: servicioForm.observaciones || undefined,
      entregado: servicioForm.entregado,
      equipos_seleccionados: servicioForm.equipos_seleccionados
    }
    
    await createFacturaServicio(payload)
    toast.success('Servicio facturado exitosamente')
    emit('success')
    handleClose()
  } catch (error: any) {
    console.error('Error facturando servicio:', error)
    toast.error(error.message || 'Error al facturar el servicio')
  } finally {
    isSaving.value = false
  }
}

// Cerrar modal
const handleClose = () => {
  if (isSaving.value) return
  
  // Reset forms
  ventaForm.cliente_id = ''
  ventaForm.forma_pago_id = ''
  ventaForm.observaciones = ''
  ventaForm.entregado = true
  ventaForm.items = [{
    inventario_id: '',
    cantidad: 1,
    tipo_precio: 'DET',
    precio: 0
  }]
  
  servicioForm.orden_servicio_id = ''
  servicioForm.forma_pago_id = ''
  servicioForm.observaciones = ''
  servicioForm.entregado = true
  servicioForm.equipos_seleccionados = []
  
  equiposOrden.value = []
  
  emit('close')
}

</script>