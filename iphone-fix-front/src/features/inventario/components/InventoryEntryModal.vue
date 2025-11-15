<!-- iphone-fix-front/src/features/inventario/components/InventoryEntryModal.vue -->
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
      <div v-if="isOpen" class="fixed inset-0 z-[9999] overflow-y-auto">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="closeModal"></div>
        
        <!-- Modal Container -->
        <div class="flex min-h-full items-center justify-center p-4">
          <div class="relative w-full max-w-6xl bg-white rounded-2xl shadow-2xl">
            
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-white">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                  <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-gray-900">Entrada de Stock</h3>
                  <p class="text-sm text-gray-500">Registra nuevos productos al inventario</p>
                </div>
              </div>
              <button 
                @click="closeModal" 
                type="button"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <!-- Body -->
            <div class="max-h-[calc(100vh-180px)] overflow-y-auto">
              <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
                
                <!-- SECCIÓN 1: Tipo de Entrada -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-5 rounded-xl border border-gray-200">
                  <label class="block text-sm font-semibold text-gray-700 mb-3">
                    🏷️ Tipo de Entrada *
                  </label>
                  <div class="flex gap-4">
                    <label class="flex-1 cursor-pointer">
                      <input
                        type="radio"
                        v-model="form.tipo_entrada"
                        value="proveedor"
                        class="peer sr-only"
                      />
                      <div class="p-4 border-2 rounded-xl transition-all peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:shadow-md hover:border-gray-300 border-gray-200 bg-white">
                        <div class="flex items-center gap-3">
                          <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center peer-checked:bg-green-200">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                          </div>
                          <div>
                            <div class="font-semibold text-gray-900">Proveedor</div>
                            <div class="text-xs text-gray-500">Compra a proveedor</div>
                          </div>
                        </div>
                      </div>
                    </label>
                    
                    <label class="flex-1 cursor-pointer">
                      <input
                        type="radio"
                        v-model="form.tipo_entrada"
                        value="cliente"
                        class="peer sr-only"
                      />
                      <div class="p-4 border-2 rounded-xl transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:shadow-md hover:border-gray-300 border-gray-200 bg-white">
                        <div class="flex items-center gap-3">
                          <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center peer-checked:bg-blue-200">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                          </div>
                          <div>
                            <div class="font-semibold text-gray-900">Cliente</div>
                            <div class="text-xs text-gray-500">Compra usados / Trade-in</div>
                          </div>
                        </div>
                      </div>
                    </label>
                  </div>
                </div>

                <!-- SECCIÓN 2: Origen (Proveedor o Cliente) -->
                <div class="bg-white border border-gray-200 rounded-xl p-5">
                  <h4 class="text-sm font-semibold text-gray-700 mb-4">
                    📍 Origen de la Entrada
                  </h4>

                  <!-- Proveedor -->
                  <div v-if="form.tipo_entrada === 'proveedor'">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Proveedor * 
                      <span class="text-xs text-gray-500 font-normal">(Buscar por nombre o NIT)</span>
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                      </div>
                      <input
                        v-model="searchProveedor"
                        @input="buscarProveedoresDebounced"
                        type="text"
                        placeholder="Buscar proveedor..."
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                        :disabled="proveedorSeleccionado !== null"
                      />
                      
                      <!-- Dropdown resultados proveedor -->
                      <div v-if="proveedoresResultados.length > 0 && !proveedorSeleccionado" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto">
                        <button
                          v-for="prov in proveedoresResultados"
                          :key="prov.id"
                          type="button"
                          @click="seleccionarProveedor(prov)"
                          class="w-full px-4 py-3 text-left hover:bg-green-50 border-b border-gray-100 last:border-b-0 transition-colors"
                        >
                          <div class="font-medium text-gray-900">{{ prov.nombre }}</div>
                          <div class="text-sm text-gray-500">{{ prov.tipo_documento }}: {{ prov.nit || 'Sin NIT' }}</div>
                        </button>
                      </div>
                      
                      <!-- Chip proveedor seleccionado -->
                      <div v-if="proveedorSeleccionado" class="mt-3 p-3 bg-green-50 border-2 border-green-200 rounded-lg flex items-center justify-between">
                        <div class="flex items-center gap-3">
                          <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                          </div>
                          <div>
                            <div class="font-semibold text-green-900">{{ proveedorSeleccionado.nombre }}</div>
                            <div class="text-sm text-green-700">{{ proveedorSeleccionado.tipo_documento }}: {{ proveedorSeleccionado.nit }}</div>
                          </div>
                        </div>
                        <button
                          type="button"
                          @click="limpiarProveedor"
                          class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                          title="Cambiar proveedor"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Cliente -->
                  <div v-if="form.tipo_entrada === 'cliente'">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Cliente * 
                      <span class="text-xs text-gray-500 font-normal">(Buscar por nombre o documento)</span>
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                      </div>
                      <input
                        v-model="searchCliente"
                        @input="fetchClientesDebounced"
                        type="text"
                        placeholder="Buscar cliente..."
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        :disabled="clienteSeleccionado !== null"
                      />
                      
                      <!-- Dropdown resultados cliente -->
                      <div v-if="clientesResultados.length > 0 && !clienteSeleccionado" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto">
                        <button
                          v-for="cli in clientesResultados"
                          :key="cli.id"
                          type="button"
                          @click="seleccionarCliente(cli)"
                          class="w-full px-4 py-3 text-left hover:bg-blue-50 border-b border-gray-100 last:border-b-0 transition-colors"
                        >
                          <div class="font-medium text-gray-900">{{ cli.nombre }}</div>
                          <div class="text-sm text-gray-500">Doc: {{ cli.documento || 'Sin documento' }}</div>
                        </button>
                      </div>
                      
                      <!-- Chip cliente seleccionado -->
                      <div v-if="clienteSeleccionado" class="mt-3 p-3 bg-blue-50 border-2 border-blue-200 rounded-lg flex items-center justify-between">
                        <div class="flex items-center gap-3">
                          <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                          </div>
                          <div>
                            <div class="font-semibold text-blue-900">{{ clienteSeleccionado.nombre }}</div>
                            <div class="text-sm text-blue-700">Doc: {{ clienteSeleccionado.documento }}</div>
                          </div>
                        </div>
                        <button
                          type="button"
                          @click="limpiarCliente"
                          class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                          title="Cambiar cliente"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- SECCIÓN 3: Detalles de Entrada -->
                <div class="bg-white border border-gray-200 rounded-xl p-5">
                  <h4 class="text-sm font-semibold text-gray-700 mb-4">
                    📋 Detalles de la Entrada
                  </h4>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Motivo -->
                    <div class="lg:col-span-2">
                      <label class="block text-sm font-medium text-gray-700 mb-2">Motivo de Ingreso *</label>
                      <select
                        v-model="form.motivo_ingreso_id"
                        required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      >
                        <option value="">Seleccione motivo...</option>
                        <option v-for="motivo in motivosIngreso" :key="motivo.id" :value="motivo.id">
                          {{ motivo.nombre }}
                        </option>
                      </select>
                    </div>

                    <!-- Estado -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Estado *</label>
                      <select
                        v-model="form.estado_entrada_id"
                        required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      >
                        <option v-for="estado in estadosEntrada" :key="estado.id" :value="estado.id">
                          {{ estado.nombre }}
                        </option>
                      </select>
                    </div>

                    <!-- Fecha -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Fecha *</label>
                      <input
                        v-model="form.fecha_entrada"
                        type="date"
                        required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      />
                    </div>

                    <!-- Lote (Opcional) -->
                    <div class="lg:col-span-4">
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Lote / Flete 
                        <span class="text-xs text-gray-500 font-normal">(Opcional)</span>
                      </label>
                      <select
                        v-model="form.lote_id"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      >
                        <option :value="null">Sin lote</option>
                        <option v-for="lote in lotes" :key="lote.id" :value="lote.id">
                          {{ lote.nombre }}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- SECCIÓN 4: Productos -->
                <div class="bg-white border border-gray-200 rounded-xl p-5">
                  <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-semibold text-gray-700">
                      📦 Productos a Ingresar
                    </h4>
                    <button
                      type="button"
                      @click="agregarProducto"
                      class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm font-medium flex items-center gap-2"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                      </svg>
                      Agregar
                    </button>
                  </div>

                  <div class="space-y-3">
                    <div
                      v-for="(item, index) in form.items"
                      :key="index"
                      class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:border-green-300 transition-colors"
                    >
                      <div class="grid grid-cols-12 gap-3 items-end">
                        
                        <!-- Producto -->
                        <div class="col-span-12 md:col-span-5">
                          <label class="block text-xs font-medium text-gray-600 mb-1">Producto *</label>
                          <div class="relative">
                            <input
                              v-model="item.search"
                              @input="() => buscarProductos(index)"
                              type="text"
                              placeholder="Buscar..."
                              class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            />
                            
                            <!-- Dropdown productos -->
                            <div v-if="item.resultados && item.resultados.length > 0" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                              <button
                                v-for="prod in item.resultados"
                                :key="prod.id"
                                type="button"
                                @click="() => seleccionarProducto(index, prod)"
                                class="w-full px-3 py-2 text-left hover:bg-gray-50 border-b border-gray-100 last:border-b-0 text-sm"
                              >
                                <div class="font-medium text-gray-900">{{ prod.codigo }}</div>
                                <div class="text-xs text-gray-500">{{ prod.nombre }}</div>
                              </button>
                            </div>
                          </div>
                        </div>

                        <!-- Cantidad -->
                        <div class="col-span-4 md:col-span-2">
                          <label class="block text-xs font-medium text-gray-600 mb-1">Cantidad *</label>
                          <input
                            v-model.number="item.cantidad"
                            type="number"
                            min="1"
                            required
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                          />
                        </div>

                        <!-- Costo -->
                        <div class="col-span-4 md:col-span-3">
                          <label class="block text-xs font-medium text-gray-600 mb-1">Costo Unit. *</label>
                          <input
                            v-model.number="item.costo_unitario"
                            type="number"
                            min="0"
                            step="0.01"
                            required
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                          />
                        </div>

                        <!-- Subtotal -->
                        <div class="col-span-3 md:col-span-1">
                          <label class="block text-xs font-medium text-gray-600 mb-1">Subtotal</label>
                          <div class="text-sm font-semibold text-gray-900 py-2">
                            ${{ (item.cantidad * item.costo_unitario).toLocaleString('es-CO') }}
                          </div>
                        </div>

                        <!-- Eliminar -->
                        <div class="col-span-1">
                          <button
                            v-if="form.items.length > 1"
                            type="button"
                            @click="eliminarProducto(index)"
                            class="w-full p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                            title="Eliminar"
                          >
                            <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- SECCIÓN 5: Observaciones -->
                <div class="bg-white border border-gray-200 rounded-xl p-5">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    📝 Observaciones
                  </label>
                  <textarea
                    v-model="form.observaciones"
                    rows="3"
                    placeholder="Notas adicionales sobre esta entrada..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none"
                  ></textarea>
                </div>

                <!-- Total -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl p-5">
                  <div class="flex items-center justify-between">
                    <div>
                      <div class="text-sm text-gray-600 font-medium">Total de la Entrada</div>
                      <div class="text-xs text-gray-500 mt-1">{{ form.items.length }} producto(s)</div>
                    </div>
                    <div class="text-right">
                      <div class="text-4xl font-bold text-green-600">
                        ${{ calculateTotal().toLocaleString('es-CO') }}
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
              <button
                @click="closeModal"
                type="button"
                class="px-6 py-2.5 bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all font-medium"
              >
                Cancelar
              </button>
              <button
                @click="handleSubmit"
                :disabled="isSubmitting || !canSubmit"
                class="px-8 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium disabled:bg-gray-300 disabled:cursor-not-allowed flex items-center gap-2 shadow-lg hover:shadow-xl"
              >
                <svg v-if="isSubmitting" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ isSubmitting ? 'Guardando...' : 'Registrar Entrada' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue'
import { toast } from 'vue3-toastify'
import { 
  createEntradaInventario,
  buscarProveedores,
  fetchEstadosEntrada
} from '../api/inventoryEntries'
import {  
  fetchLotesOptions, 
  fetchMotivosIngresoOptions
} from '../api/inventario'
import type { Proveedor, Cliente, EstadoEntrada } from '../types/inventoryEntry'
import { fetchClientes } from '../../OrdenServicio/api/clientes'
import { fetchInventario } from '../api/inventario'
import type { Option } from '../../../shared/types/common'

// Props & Emits
interface Props {
  isOpen: boolean
}

const props = defineProps<Props>()
const emit = defineEmits<{
  close: []
  success: []
}>()

// State
const isSubmitting = ref(false)
const motivosIngreso = ref<Option[]>([])
const lotes = ref<Option[]>([])
const estadosEntrada = ref<EstadoEntrada[]>([])

// Proveedor
const searchProveedor = ref('')
const proveedoresResultados = ref<Proveedor[]>([])
const proveedorSeleccionado = ref<Proveedor | null>(null)

// Cliente
const searchCliente = ref('')
const clientesResultados = ref<Cliente[]>([])
const clienteSeleccionado = ref<Cliente | null>(null)

interface FormItem {
  inventario_id: number | string
  cantidad: number
  costo_unitario: number
  search: string
  resultados: any[]
}

const form = reactive<{
  tipo_entrada: 'proveedor' | 'cliente'
  proveedor_id: number
  cliente_id: number
  motivo_ingreso_id: number | string
  estado_entrada_id: number
  lote_id: number | null
  fecha_entrada: string
  observaciones: string
  items: FormItem[]
}>({
  tipo_entrada: 'proveedor',
  proveedor_id: 0,
  cliente_id: 0,
  motivo_ingreso_id: '',
  estado_entrada_id: 1,
  lote_id: null,
  fecha_entrada: new Date().toISOString().split('T')[0],
  observaciones: '',
  items: [{
    inventario_id: '',
    cantidad: 1,
    costo_unitario: 0,
    search: '',
    resultados: []
  }]
})

// Computed
const canSubmit = computed(() => {
  const tieneOrigen = form.tipo_entrada === 'proveedor' 
    ? form.proveedor_id > 0 
    : form.cliente_id > 0
  
  return (
    tieneOrigen &&
    form.motivo_ingreso_id !== '' &&
    form.items.length > 0 &&
    form.items.every(item => 
      item.inventario_id !== '' && 
      item.cantidad > 0 && 
      item.costo_unitario >= 0
    )
  )
})

// Load options solo cuando se abre el modal
watch(() => props.isOpen, async (isOpen) => {
  if (isOpen) {
    
    try {
      // Cargar motivos de ingreso
      try {
        const motivosResult = await fetchMotivosIngresoOptions()
        motivosIngreso.value = motivosResult
      } catch (error: any) {
        throw error
      }

      // Cargar lotes
      try {
        const lotesResult = await fetchLotesOptions()
        lotes.value = lotesResult
      } catch (error: any) {
        throw error
      }

      // Cargar estados de entrada
      try {
        const estadosResult = await fetchEstadosEntrada()
        estadosEntrada.value = estadosResult
      } catch (error: any) {
        throw error
      }

      
    } catch (error: any) {
      const errorMsg = error?.response?.data?.message || error?.message || 'Error al cargar las opciones'
      toast.error(`Error: ${errorMsg}`)
    }
  }
})

// Limpiar al cambiar tipo
watch(() => form.tipo_entrada, () => {
  limpiarProveedor()
  limpiarCliente()
})

// Búsqueda de proveedores con debounce
let debounceTimerProveedor: ReturnType<typeof setTimeout>
const buscarProveedoresDebounced = () => {
  clearTimeout(debounceTimerProveedor)
  debounceTimerProveedor = setTimeout(async () => {
    if (searchProveedor.value.length < 2) {
      proveedoresResultados.value = []
      return
    }
    
    try {
      const resultados = await buscarProveedores(searchProveedor.value)
      proveedoresResultados.value = resultados
    } catch (error: any) {
      proveedoresResultados.value = []
    }
  }, 300)
}

const seleccionarProveedor = (prov: Proveedor) => {
  proveedorSeleccionado.value = prov
  form.proveedor_id = prov.id
  searchProveedor.value = `${prov.nombre} - ${prov.tipo_documento}: ${prov.nit}`
  proveedoresResultados.value = []
}

const limpiarProveedor = () => {
  proveedorSeleccionado.value = null
  form.proveedor_id = 0
  searchProveedor.value = ''
  proveedoresResultados.value = []
}

// Búsqueda de clientes con debounce
let debounceTimerCliente: ReturnType<typeof setTimeout>
const fetchClientesDebounced = () => {
  clearTimeout(debounceTimerCliente)
  debounceTimerCliente = setTimeout(async () => {
    if (searchCliente.value.length < 2) {
      clientesResultados.value = []
      return
    }
    
    
    try {
      const response = await fetchClientes({ q: searchCliente.value })
      clientesResultados.value = response.data || []
    } catch (error: any) {
      clientesResultados.value = []
    }
  }, 300)
}

const seleccionarCliente = (cli: Cliente) => {
  clienteSeleccionado.value = cli
  form.cliente_id = cli.id
  searchCliente.value = `${cli.nombre} - Doc: ${cli.documento}`
  clientesResultados.value = []
}

const limpiarCliente = () => {
  clienteSeleccionado.value = null
  form.cliente_id = 0
  searchCliente.value = ''
  clientesResultados.value = []
}

// Productos
const buscarProductos = async (index: number) => {
  const query = form.items[index].search?.trim() || ''

  if (query.length < 2) {
    form.items[index].resultados = []
    return
  }

  try {
    const res = await fetchInventario({ q: query, per_page: 10 })
    form.items[index].resultados = res.data || []
  } catch (error: any) {
    form.items[index].resultados = []
  }
}

const seleccionarProducto = (index: number, producto: any) => {
  form.items[index].inventario_id = producto.id
  form.items[index].search = `${producto.codigo} - ${producto.nombre}`
  form.items[index].resultados = []
  form.items[index].costo_unitario = Number(producto.costo_mayor || producto.precio || 0)
}

const agregarProducto = () => {
  form.items.push({
    inventario_id: '',
    cantidad: 1,
    costo_unitario: 0,
    search: '',
    resultados: []
  })
}

const eliminarProducto = (index: number) => {
  if (form.items.length > 1) {
    form.items.splice(index, 1)
  }
}

const calculateTotal = () => {
  return form.items.reduce((sum, item) => sum + (item.cantidad * item.costo_unitario), 0)
}

// Submit
const handleSubmit = async () => {
  if (!canSubmit.value) {
    console.warn('⚠️ Formulario incompleto')
    toast.warning('Complete todos los campos requeridos')
    return
  }

  console.group('📤 Enviando entrada de inventario')
  isSubmitting.value = true

  try {
    const payload = {
      tipo_entrada: form.tipo_entrada,
      proveedor_id: form.tipo_entrada === 'proveedor' ? form.proveedor_id : null,
      cliente_id: form.tipo_entrada === 'cliente' ? form.cliente_id : null,
      motivo_ingreso_id: Number(form.motivo_ingreso_id),
      estado_entrada_id: form.estado_entrada_id,
      lote_id: form.lote_id,
      fecha_entrada: form.fecha_entrada,
      observaciones: form.observaciones || null,
      items: form.items.map(item => ({
        inventario_id: Number(item.inventario_id),
        cantidad: item.cantidad,
        costo_unitario: item.costo_unitario
      }))
    }

    await createEntradaInventario(payload)
    
    toast.success('¡Entrada registrada exitosamente!')
    emit('success')
    resetForm()
    closeModal()
  } catch (error: any) {
    console.error('❌ Error al crear entrada:', {
      message: error.message,
      response: error.response?.data,
      status: error.response?.status,
      url: error.config?.url
    })
    console.groupEnd()
    
    const message = error?.response?.data?.message || error?.message || 'Error al registrar la entrada'
    toast.error(message)
  } finally {
    isSubmitting.value = false
  }
}

const resetForm = () => {
  Object.assign(form, {
    tipo_entrada: 'proveedor',
    proveedor_id: 0,
    cliente_id: 0,
    motivo_ingreso_id: '',
    estado_entrada_id: 1,
    lote_id: null,
    fecha_entrada: new Date().toISOString().split('T')[0],
    observaciones: '',
    items: [{
      inventario_id: '',
      cantidad: 1,
      costo_unitario: 0,
      search: '',
      resultados: []
    }]
  })
  limpiarProveedor()
  limpiarCliente()
}

const closeModal = () => {
  if (!isSubmitting.value) {
    emit('close')
  }
}
</script>

<style scoped>
.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s ease;
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
}
</style>