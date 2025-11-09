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
          @click="$emit('close')"
        ></div>

        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
          <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-gray-900">Nueva Orden de Servicio</h3>
                  <p class="text-sm text-gray-500">Crea una nueva orden para un cliente</p>
                </div>
              </div>
              <button
                @click="$emit('close')"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Formulario -->
            <form @submit.prevent="guardar" class="p-6">
              <div class="space-y-5">
                <!-- Cliente buscable -->
                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-gray-700">
                    Cliente *
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                      </svg>
                    </div>
                    <input
                      v-model="searchCliente"
                      @input="buscarClientes"
                      required
                      type="text"
                      placeholder="Buscar por documento o apellido..."
                      class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
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
                        class="px-4 py-3 hover:bg-green-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors"
                      >
                        <div class="font-medium text-gray-900">{{ c.nombre }}</div>
                        <div class="text-sm text-gray-500">{{ c.documento }}</div>
                      </li>
                    </ul>
                  </div>
                </div>

                <!-- Observaciones -->
                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-gray-700">
                    Observaciones generales
                  </label>
                  <div class="relative">
                    <div class="absolute top-3 left-0 pl-3 flex items-start pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </div>
                    <textarea
                      v-model="form.observaciones_generales"
                      rows="3"
                      placeholder="Descripción del trabajo a realizar..."
                      class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors resize-none"
                    ></textarea>
                  </div>
                </div>

                <!-- Fechas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                  <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                      Fecha de creación
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                      </div>
                      <input
                        type="date"
                        v-model="form.fecha_creacion"
                        readonly
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed text-gray-600"
                      />
                    </div>
                  </div>
                  
                  <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                      Fecha posible de entrega
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                      </div>
                      <input
                        type="date"
                        v-model="form.fecha_cierre"
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                      />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Botones -->
              <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
                <button
                  type="button"
                  @click="$emit('close')"
                  class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  class="px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium flex items-center gap-2"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  Crear Orden
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
import { ref } from 'vue'
import type { Cliente } from '../types/cliente'
import type { CreateOrdenPayload, OrdenServicio } from '../types/orden'
import { createOrden } from '../api/orden'
import { fetchClientes } from '../api/clientes'

import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

const { open } = defineProps<{
  open: boolean
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'created', orden: OrdenServicio): void
}>()

// Estado búsqueda cliente
const searchCliente = ref('')
const clientesFiltrados = ref<Cliente[]>([])
const selectedClienteId = ref<number | ''>('')

// Formulario
const form = ref<CreateOrdenPayload>({
  estado: 'pendiente',
  observaciones_generales: '',
  fecha_creacion: new Date().toISOString().split('T')[0],
  fecha_cierre: null
})

// Buscar clientes en vivo
async function buscarClientes() {
  if (searchCliente.value.length < 2) {
    clientesFiltrados.value = []
    return
  }
  try {
    const res = await fetchClientes({ q: searchCliente.value, per_page: 5 })
    clientesFiltrados.value = res.data
  } catch {
    clientesFiltrados.value = []
    toast.error('No se pudo cargar la lista de clientes. Intenta de nuevo.')
  }
}

function seleccionarCliente(cliente: Cliente) {
  selectedClienteId.value = cliente.id
  searchCliente.value = `${cliente.nombre} - ${cliente.documento}`
  clientesFiltrados.value = []
}

// Guardar orden
async function guardar() {
  if (!selectedClienteId.value) {
    toast.warning('Debes seleccionar un cliente antes de crear la orden.')
    return
  }
  try {
    const nueva = await createOrden(Number(selectedClienteId.value), form.value)
    
    // Resetear formulario
    form.value = {
      estado: 'pendiente',
      observaciones_generales: '',
      fecha_creacion: new Date().toISOString().split('T')[0],
      fecha_cierre: null
    }
    searchCliente.value = ''
    selectedClienteId.value = ''
    
    toast.success('¡Orden creada exitosamente! 🎉', { autoClose: 3000 })
    emit('created', nueva)
    emit('close')
  } catch (e: any) {
    const msg = e?.response?.data?.message || 'No se pudo crear la orden'
    toast.error(msg, { autoClose: 3000 })
  }
}
</script>