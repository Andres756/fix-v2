<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[999] p-4"
      @click.self="handleClose"
    >
      <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-xl font-semibold text-gray-900">
            Crear Nuevo Lote
          </h2>
          <p class="text-sm text-gray-500 mt-1">
            Registra un nuevo lote de inventario
          </p>
        </div>

        <!-- Body -->
        <form @submit.prevent="guardar" class="px-6 py-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Número de lote -->
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Número de Lote
                <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.numero_lote"
                type="text"
                placeholder="Ej: LOTE-2025-001"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :class="{ 'border-red-500': errors.numero_lote }"
                required
              />
              <p v-if="errors.numero_lote" class="text-sm text-red-600 mt-1">
                {{ errors.numero_lote }}
              </p>
            </div>

            <!-- Proveedor -->
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Proveedor
              </label>
              <input
                v-model="busquedaProveedor"
                type="text"
                placeholder="Buscar proveedor..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                @input="buscarProveedores"
              />
              
              <!-- Dropdown de resultados -->
              <div
                v-if="proveedoresEncontrados.length > 0"
                class="mt-2 max-h-48 overflow-y-auto border border-gray-300 rounded-lg bg-white shadow-lg"
              >
                <div
                  v-for="proveedor in proveedoresEncontrados"
                  :key="proveedor.id"
                  @click="seleccionarProveedor(proveedor)"
                  class="px-4 py-2 hover:bg-blue-50 cursor-pointer"
                >
                  <p class="font-medium text-gray-900">{{ proveedor.nombre }}</p>
                  <p class="text-sm text-gray-500">NIT: {{ proveedor.nit || 'N/A' }}</p>
                </div>
              </div>

              <!-- Proveedor seleccionado -->
              <div v-if="form.proveedor_id && proveedorSeleccionado" class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-between">
                <div>
                  <p class="font-medium text-gray-900">{{ proveedorSeleccionado.nombre }}</p>
                  <p class="text-sm text-gray-500">NIT: {{ proveedorSeleccionado.nit || 'N/A' }}</p>
                </div>
                <button
                  type="button"
                  @click="limpiarProveedor"
                  class="text-red-600 hover:text-red-700"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Costo de flete -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Costo de Flete
              </label>
              <input
                v-model.number="form.costo_flete"
                type="number"
                step="0.01"
                min="0"
                placeholder="0.00"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            <!-- Fecha de ingreso -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Fecha de Ingreso
              </label>
              <input
                v-model="form.fecha_ingreso"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            <!-- Notas -->
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Notas
              </label>
              <textarea
                v-model="form.notas"
                rows="3"
                placeholder="Observaciones sobre el lote..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
              ></textarea>
            </div>
          </div>
        </form>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
          <button
            @click="handleClose"
            type="button"
            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors"
            :disabled="saving"
          >
            Cancelar
          </button>
          <button
            @click="guardar"
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="!puedeGuardar || saving"
          >
            {{ saving ? 'Guardando...' : 'Guardar Lote' }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { toast } from 'vue3-toastify'
import { createLote, buscarProveedores as buscarProveedoresAPI } from '../../inventario/api/inventoryEntries'
import type { Lote, Proveedor, CreateLotePayload } from '../../inventario/types/inventoryEntry'

// Props
interface Props {
  isOpen: boolean
  proveedorId?: number | null
}

const props = defineProps<Props>()

// Emits
const emit = defineEmits<{
  close: []
  success: [lote: Lote]
}>()

// Estado
const saving = ref(false)
const busquedaProveedor = ref('')
const proveedoresEncontrados = ref<Proveedor[]>([])
const proveedorSeleccionado = ref<Proveedor | null>(null)

const form = ref<CreateLotePayload>({
  numero_lote: '',
  proveedor_id: props.proveedorId || null,
  costo_flete: 0,
  fecha_ingreso: new Date().toISOString().split('T')[0],
  notas: '',
})

const errors = ref<Record<string, string>>({})

// Computed
const puedeGuardar = computed(() => {
  return form.value.numero_lote.trim() !== ''
})

// Métodos
const handleClose = () => {
  emit('close')
}

const buscarProveedores = async () => {
  const query = busquedaProveedor.value.trim()
  
  if (query.length < 2) {
    proveedoresEncontrados.value = []
    return
  }

  try {
    proveedoresEncontrados.value = await buscarProveedoresAPI(query)
  } catch (error) {
    console.error('Error buscando proveedores:', error)
  }
}

const seleccionarProveedor = (proveedor: Proveedor) => {
  form.value.proveedor_id = proveedor.id
  proveedorSeleccionado.value = proveedor
  busquedaProveedor.value = proveedor.nombre
  proveedoresEncontrados.value = []
}

const limpiarProveedor = () => {
  form.value.proveedor_id = null
  proveedorSeleccionado.value = null
  busquedaProveedor.value = ''
  proveedoresEncontrados.value = []
}

const validar = (): boolean => {
  errors.value = {}

  if (!form.value.numero_lote.trim()) {
    errors.value.numero_lote = 'El número de lote es requerido'
  }

  return Object.keys(errors.value).length === 0
}

const guardar = async () => {
  if (!validar() || !puedeGuardar.value) return

  saving.value = true
  try {
    const nuevoLote = await createLote(form.value)
    
    toast.success('Lote creado correctamente')
    emit('success', nuevoLote)
  } catch (error: any) {
    console.error('Error creando lote:', error)
    
    if (error?.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      toast.error(error?.response?.data?.message || 'Error al crear el lote')
    }
  } finally {
    saving.value = false
  }
}

// Generar número de lote automático
const generarNumeroLote = () => {
  const fecha = new Date()
  const año = fecha.getFullYear()
  const mes = String(fecha.getMonth() + 1).padStart(2, '0')
  const contador = Math.floor(Math.random() * 1000).toString().padStart(3, '0')
  
  form.value.numero_lote = `LOTE-${año}${mes}-${contador}`
}

// Lifecycle
onMounted(() => {
  if (!form.value.numero_lote) {
    generarNumeroLote()
  }
})
</script>