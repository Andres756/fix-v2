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
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="closeModal"></div>
        
        <div class="flex min-h-full items-center justify-center p-4">
          <div class="relative w-full max-w-5xl bg-white rounded-2xl shadow-2xl">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-gray-900">Entrada de Stock</h3>
                  <p class="text-sm text-gray-500">Registra nuevos productos al inventario</p>
                </div>
              </div>
              <button @click="closeModal" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <!-- Body -->
            <div class="max-h-[calc(100vh-200px)] overflow-y-auto">
              <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
                <!-- Información de Entrada: Proveedor, Motivo y Lote -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <!-- Proveedor -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Proveedor *</label>
                    <select v-model="form.proveedor_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                      <option value="">Seleccione un proveedor</option>
                      <option v-for="proveedor in proveedores" :key="proveedor.id" :value="proveedor.id">
                        {{ proveedor.nombre }}
                      </option>
                    </select>
                  </div>

                  <!-- Motivo de Ingreso -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Motivo de Ingreso *</label>
                    <select v-model="form.motivo_ingreso_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                      <option value="">Seleccione un motivo</option>
                      <option v-for="motivo in motivos" :key="motivo.id" :value="motivo.id">{{ motivo.nombre }}</option>
                    </select>
                  </div>

                  <!-- Lote -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lote (Flete) *</label>
                    <select v-model="form.lote_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                      <option value="">Seleccione un lote</option>
                      <option v-for="lote in lotes" :key="lote.id" :value="lote.id">{{ lote.nombre }}</option>
                    </select>
                  </div>
                </div>

                <!-- Productos -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-3">Productos</label>
                  <div class="space-y-3">
                    <div v-for="(item, index) in form.items" :key="index" class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                      <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                        <div class="md:col-span-5">
                          <label class="block text-xs text-gray-600 mb-1">Producto *</label>
                          <select v-model="item.inventario_id" @change="onProductSelect(index)" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Seleccione</option>
                            <option v-for="producto in productos" :key="producto.id" :value="producto.id">{{ producto.nombre }} ({{ producto.codigo }})</option>
                          </select>
                        </div>
                        <div class="md:col-span-2">
                          <label class="block text-xs text-gray-600 mb-1">Cantidad *</label>
                          <input v-model.number="item.cantidad" type="number" min="1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"/>
                        </div>
                        <div class="md:col-span-2">
                          <label class="block text-xs text-gray-600 mb-1">Costo Unit. *</label>
                          <input v-model.number="item.costo_unitario" type="number" step="0.01" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"/>
                        </div>
                        <div class="md:col-span-2">
                          <label class="block text-xs text-gray-600 mb-1">Subtotal</label>
                          <input :value="(item.cantidad * item.costo_unitario).toFixed(2)" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600"/>
                        </div>
                        <div class="md:col-span-1">
                          <button v-if="form.items.length > 1" @click="removeItem(index)" type="button" class="w-full px-2 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                            <svg class="h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <button @click="addItem" type="button" class="mt-3 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Agregar Producto
                  </button>
                </div>

                <!-- Fecha y Observaciones -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Entrada</label>
                    <input v-model="form.fecha_entrada" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"/>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                    <input v-model="form.observaciones" type="text" placeholder="Notas adicionales..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"/>
                  </div>
                </div>

                <!-- Total -->
                <div class="flex justify-end pt-4 border-t border-gray-200">
                  <div class="text-right">
                    <p class="text-sm text-gray-600">Total</p>
                    <p class="text-3xl font-bold text-gray-900">${{ calculateTotal().toLocaleString('es-CO', { minimumFractionDigits: 2 }) }}</p>
                  </div>
                </div>
              </form>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t flex justify-end gap-3">
              <button @click="closeModal" type="button" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">Cancelar</button>
              <button @click="handleSubmit" :disabled="isSubmitting" class="px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium disabled:bg-green-300 disabled:cursor-not-allowed flex items-center gap-2">
                <svg v-if="isSubmitting" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
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
import { ref, reactive, watch } from 'vue'
import { toast } from 'vue3-toastify'
import { createEntradaInventario } from '../api/inventoryEntries'
import { 
  fetchInventario, 
  fetchLotesOptions, 
  fetchMotivosIngresoOptions,
  fetchProveedoresOptions  // ✅ AGREGAR
} from '../api/inventario'
import type { Inventario } from '../types/inventario'
import type { Option } from '../../../shared/types/common'

interface FormItem {
  inventario_id: number | string
  cantidad: number
  costo_unitario: number
}

const props = defineProps<{ isOpen: boolean }>()
const emit = defineEmits<{ (e: 'close'): void; (e: 'success'): void }>()

const productos = ref<Inventario[]>([])
const lotes = ref<Option[]>([])
const motivos = ref<Option[]>([])
const proveedores = ref<Option[]>([])  // ✅ AGREGAR

const isSubmitting = ref(false)

const form = reactive({
  proveedor_id: '',  // ✅ AGREGAR
  motivo_ingreso_id: '',
  lote_id: '',
  fecha_entrada: new Date().toISOString().split('T')[0],
  observaciones: '',
  items: [{ inventario_id: '', cantidad: 1, costo_unitario: 0 }] as FormItem[]
})

const fetchData = async () => {
  try {
    const [productosResponse, lotesData, motivosData, proveedoresData] = await Promise.all([
      fetchInventario({}),
      fetchLotesOptions(),
      fetchMotivosIngresoOptions(),
      fetchProveedoresOptions()  // ✅ AGREGAR
    ])
    productos.value = productosResponse.data || []
    lotes.value = lotesData
    motivos.value = motivosData
    proveedores.value = proveedoresData  // ✅ AGREGAR
  } catch (error) {
    console.error('Error al cargar datos:', error)
    toast.error('Error al cargar la información necesaria')
  }
}

const onProductSelect = (index: number) => {
  const producto = productos.value.find(p => p.id === Number(form.items[index].inventario_id))
  if (producto && producto.costo) {
    form.items[index].costo_unitario = Number(producto.costo)
  }
}

const addItem = () => {
  form.items.push({ inventario_id: '', cantidad: 1, costo_unitario: 0 })
}

const removeItem = (index: number) => {
  form.items.splice(index, 1)
}

const calculateTotal = (): number => {
  return form.items.reduce((sum, item) => sum + (item.cantidad * item.costo_unitario), 0)
}

const handleSubmit = async () => {
  isSubmitting.value = true
  try {
    // ✅ VALIDAR CAMPOS OBLIGATORIOS
    if (!form.proveedor_id) {
      toast.warning('Debe seleccionar un proveedor')
      return
    }
    
    if (!form.motivo_ingreso_id) {
      toast.warning('Debe seleccionar un motivo de ingreso')
      return
    }
    
    // ❌ REMOVER validación de lote_id (ya no es obligatorio)

    // Validar items
    const hasInvalidItems = form.items.some(
      item => !item.inventario_id || item.cantidad <= 0 || item.costo_unitario < 0
    )
    
    if (hasInvalidItems) {
      toast.warning('Por favor complete todos los campos de productos correctamente')
      return
    }

    // ✅ Enviar datos (lote_id puede ser null)
    await createEntradaInventario({
      proveedor_id: Number(form.proveedor_id),
      motivo_ingreso_id: Number(form.motivo_ingreso_id),
      lote_id: form.lote_id ? Number(form.lote_id) : null, // ✅ Permitir null
      fecha_entrada: form.fecha_entrada,
      observaciones: form.observaciones || null,
      items: form.items.map(item => ({
        inventario_id: Number(item.inventario_id),
        cantidad: Number(item.cantidad),
        costo_unitario: Number(item.costo_unitario)
      }))
    })

    toast.success('Entrada registrada exitosamente')
    emit('success')
    closeModal()
    resetForm()
  } catch (error: any) {
    console.error('Error:', error)
    
    if (error.response?.status === 422 && error.response?.data?.errors) {
      const firstError = Object.values(error.response.data.errors)[0]
      if (Array.isArray(firstError) && firstError.length > 0) {
        toast.error(firstError[0])
      } else {
        toast.error('Error de validación. Verifique los campos.')
      }
    } else {
      toast.error(error.response?.data?.message || 'Error al registrar la entrada')
    }
  } finally {
    isSubmitting.value = false
  }
}

const resetForm = () => {
  form.proveedor_id = ''  // ✅ AGREGAR
  form.motivo_ingreso_id = ''
  form.lote_id = ''
  form.fecha_entrada = new Date().toISOString().split('T')[0]
  form.observaciones = ''
  form.items = [{ inventario_id: '', cantidad: 1, costo_unitario: 0 }]
}

const closeModal = () => {
  resetForm()
  emit('close')
}

watch(() => props.isOpen, (isOpen) => {
  if (isOpen) fetchData()
})
</script>