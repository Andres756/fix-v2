<!-- Archivo: src/features/inventario/components/EditInventarioModal.vue -->
<template>
  <Teleport to="body">
    <Transition name="modal">
      <div 
        v-if="open" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        @click.self="emit('close')"
      >
        <div 
          class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden"
          @click.stop
        >
          <!-- Header -->
          <div class="sticky top-0 z-10 bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
              </div>
              <div>
                <h2 class="text-xl font-bold text-white">Editar Producto</h2>
                <p class="text-sm text-blue-100">Actualiza la información del producto</p>
              </div>
            </div>
            <button 
              @click="emit('close')" 
              class="p-2 hover:bg-white/20 rounded-lg transition-colors duration-200"
            >
              <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Loading State -->
          <div v-if="loading" class="p-12 flex flex-col items-center justify-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
            <p class="text-gray-600">Cargando datos...</p>
          </div>

          <!-- Content -->
          <form v-else @submit.prevent="guardar" class="flex flex-col max-h-[calc(90vh-80px)]">
            <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
              
              <!-- Información Básica -->
              <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl p-6 space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                  <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  Información Básica
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Nombre -->
                  <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700">
                      Nombre <span class="text-red-500">*</span>
                    </label>
                    <input
                      v-model="form.nombre"
                      type="text"
                      required
                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                      placeholder="Ej: iPhone 14"
                    />
                  </div>

                  <!-- Código -->
                  <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700">
                      Código <span class="text-red-500">*</span>
                    </label>
                    <input
                      v-model="form.codigo"
                      type="text"
                      required
                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                      placeholder="Ej: IPH14-001"
                    />
                  </div>

                  <!-- Nombre Detallado -->
                  <div class="space-y-2 md:col-span-2">
                    <label class="text-sm font-semibold text-gray-700">
                      Nombre Detallado <span class="text-red-500">*</span>
                    </label>
                    <input
                      v-model="form.nombre_detallado"
                      type="text"
                      required
                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                      placeholder="Ej: iPhone 14 Pro Max 256GB Negro - IMEI: 123456"
                    />
                  </div>

                  <!-- Tipo -->
                  <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700">
                      Tipo <span class="text-red-500">*</span>
                    </label>
                    <select
                      v-model="form.tipo_inventario_id"
                      required
                      disabled
                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-100 cursor-not-allowed"
                    >
                      <option value="">Seleccionar tipo...</option>
                      <option v-for="t in tipos" :key="t.id" :value="t.id">
                        {{ t.nombre }}
                      </option>
                    </select>
                    <p class="text-xs text-gray-500">El tipo de inventario no se puede cambiar</p>
                  </div>

                  <!-- Categoría -->
                  <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700">
                      Categoría <span class="text-red-500">*</span>
                    </label>
                    <select
                      v-model="form.categoria_id"
                      required
                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"
                    >
                      <option value="">Seleccionar categoría...</option>
                      <option v-for="c in categorias" :key="c.id" :value="c.id">
                        {{ c.nombre }}
                      </option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Precios -->
              <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6 space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                  <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  Precios y Costos
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700">
                      Precio Venta <span class="text-red-500">*</span>
                    </label>
                    <input
                      v-model.number="form.precio"
                      type="number"
                      required
                      min="1"
                      step="1"
                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all"
                    />
                  </div>

                  <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700">
                      Costo Mayor <span class="text-red-500">*</span>
                    </label>
                    <input
                      v-model.number="form.costo_mayor"
                      type="number"
                      required
                      min="1"
                      step="1"
                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all"
                    />
                  </div>

                  <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700">
                      Stock Mínimo <span class="text-red-500">*</span>
                    </label>
                    <input
                      v-model.number="form.stock_minimo"
                      type="number"
                      required
                      min="1"
                      step="1"
                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all"
                    />
                  </div>
                </div>
              </div>

              <!-- Detalles según tipo -->
              <transition name="slide-fade" mode="out-in">
                <EquipoFields 
                  v-if="isEquipo" 
                  key="equipo"
                  v-model:detalle="detalle_equipo" 
                />
                <ProductoFields 
                  v-else-if="isProducto" 
                  key="producto"
                  v-model:detalle="detalle_producto" 
                />
                <RepuestoFields 
                  v-else-if="isRepuesto"
                  key="repuesto"
                  v-model:detalle="detalle_repuesto" 
                />
              </transition>

              <!-- Imagen -->
              <div class="space-y-4">
                <label class="text-sm font-semibold text-gray-700">Imagen del Producto</label>
                <ImageUpload 
                  :preview-url="previewUrl"
                  @update="onImagen" 
                />
              </div>

              <!-- Error Message -->
              <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-sm text-red-800">{{ error }}</p>
              </div>
            </div>

            <!-- Footer con Acciones -->
            <div class="sticky bottom-0 bg-gray-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-200">
              <button
                type="button"
                @click="emit('close')"
                :disabled="saving"
                class="px-6 py-2.5 border-2 border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-100 transition-all duration-200"
              >
                Cancelar
              </button>
              <button
                type="submit"
                :disabled="saving"
                class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg font-medium hover:from-blue-700 hover:to-purple-700 transition-all duration-200 flex items-center gap-2 disabled:opacity-50"
              >
                <svg v-if="saving" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ saving ? 'Guardando...' : 'Actualizar Producto' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { toast } from 'vue3-toastify'
import { 
  fetchInventarioById, 
  updateInventario,
  fetchTiposInventarioOptions,
  fetchCategoriasOptions,
  type UpdateInventarioPayload
} from '../api/inventario'
import EquipoFields from './forms/EquipoFields.vue'
import ProductoFields from './forms/ProductoFields.vue'
import RepuestoFields from './forms/RepuestoFields.vue'

interface Props {
  open: boolean
  inventarioId: number | null
}

const props = defineProps<Props>()
const emit = defineEmits<{
  close: []
  updated: []
}>()

// Estados
const loading = ref(false)
const saving = ref(false)
const error = ref<string | null>(null)

const tipos = ref<any[]>([])
const categorias = ref<any[]>([])

// Form
const form = ref({
  nombre: '',
  nombre_detallado: '',
  codigo: '',
  tipo_inventario_id: null as any,
  categoria_id: null as any,
  stock_minimo: 1,
  precio: 0,
  costo_mayor: 0,
  tipo_impuesto: 'n/a' as const,
  valor_impuesto: 0,
  notas: ''
})

const detalle_equipo = ref({
  modelo_equipo_id: null as number | null,
  imei_1: '',
  imei_2: '',
  estado_fisico: '',
  version_ios: '',
  almacenamiento: '',
  color: ''
})

const detalle_producto = ref({
  material: '',
  compatibilidad: '',
  tipo_accesorio: ''
})

const detalle_repuesto = ref({
  modelo_compatible: '',
  tipo_repuesto: '',
  referencia_fabricante: '',
  garantia_meses: null as number | null
})

// Imagen
let nuevaImagen: File | null = null
const previewUrl = ref<string | null>(null)

// Computed
const isEquipo = computed(() => Number(form.value.tipo_inventario_id) === 1)
const isProducto = computed(() => Number(form.value.tipo_inventario_id) === 2)
const isRepuesto = computed(() => Number(form.value.tipo_inventario_id) === 3)

// ✅ FUNCIÓN AUXILIAR para manejar valores nulos/undefined
function safeString(value: any): string {
  return value ?? ''
}

function safeNumber(value: any, defaultValue = 0): number {
  const num = Number(value)
  return isNaN(num) ? defaultValue : num
}

// Métodos
async function cargarDatos() {
  if (!props.inventarioId) return

  loading.value = true
  error.value = null

  try {
    const [inventario, tiposData] = await Promise.all([
      fetchInventarioById(props.inventarioId),
      fetchTiposInventarioOptions()
    ])

    tipos.value = tiposData

    // ✅ CORREGIDO: El backend envía 'tipo_id' en lugar de 'tipo_inventario_id'
    const tipoIdRaw = 
      inventario?.tipo_inventario_id ?? 
      inventario?.tipo?.id ?? 
      null

    const tipoId = tipoIdRaw ? Number(tipoIdRaw) : null


    if (!tipoId || isNaN(tipoId) || tipoId <= 0) {
      console.error('❌ tipo_inventario_id inválido:', tipoIdRaw)
      toast.error('El producto no tiene un tipo de inventario válido')
      emit('close')
      return
    }

    // Cargar categorías del tipo
    categorias.value = await fetchCategoriasOptions(tipoId)

    // Llenar formulario con validaciones
    form.value = {
      nombre: safeString(inventario?.nombre),
      nombre_detallado: inventario?.nombre_detallado ?? inventario?.nombre_full ?? '', // ✅ CORREGIDO
      codigo: safeString(inventario?.codigo),
      tipo_inventario_id: tipoId,
      categoria_id: safeNumber(inventario?.categoria_id),
      stock_minimo: safeNumber((inventario as any)?.stock_min ?? inventario?.stock_minimo, 1), // ✅ CORREGIDO
      precio: safeNumber(inventario?.precio),
      costo_mayor: safeNumber(inventario?.costo_mayor),
      tipo_impuesto: (inventario?.tipo_impuesto as any) || 'n/a',
      valor_impuesto: safeNumber(inventario?.valor_impuesto),
      notas: (inventario as any)?.notas ?? '' // ✅ CORREGIDO
    }


    // ✅ CORREGIDO: Usar acceso con 'as any' para propiedades no reconocidas por TypeScript
    const detalleEquipoData = (inventario as any)?.detalle_equipo ?? inventario?.detalleEquipo
    const detalleProductoData = (inventario as any)?.detalle_producto ?? inventario?.detalleProducto
    const detalleRepuestoData = (inventario as any)?.detalle_repuesto ?? inventario?.detalleRepuesto

    if (detalleEquipoData) {

      // Asignamos el modelo y el modelo_equipo_id correctamente
      detalle_equipo.value = {
        modelo_equipo_id: detalleEquipoData.modelo ? detalleEquipoData.modelo.id : null,  // Asignamos el ID del modelo
        imei_1: safeString(detalleEquipoData.imei_1),
        imei_2: safeString(detalleEquipoData.imei_2),
        estado_fisico: safeString(detalleEquipoData.estado_fisico),
        version_ios: safeString(detalleEquipoData.version_ios),
        almacenamiento: safeString(detalleEquipoData.almacenamiento),
        color: safeString(detalleEquipoData.color),
        modelo: {
          id: detalleEquipoData.modelo?.id ?? null,
          nombre: detalleEquipoData.modelo?.nombre ?? '',
          marca: detalleEquipoData.modelo?.marca ?? ''
        }
      };
    }

    if (detalleProductoData) {
      detalle_producto.value = {
        material: safeString(detalleProductoData.material),
        compatibilidad: safeString(detalleProductoData.compatibilidad),
        tipo_accesorio: safeString(detalleProductoData.tipo_accesorio)
      }
    }

    if (detalleRepuestoData) {
      detalle_repuesto.value = {
        modelo_compatible: safeString(detalleRepuestoData.modelo_compatible),
        tipo_repuesto: safeString(detalleRepuestoData.tipo_repuesto),
        referencia_fabricante: safeString(detalleRepuestoData.referencia_fabricante),
        garantia_meses: detalleRepuestoData.garantia_meses ? Number(detalleRepuestoData.garantia_meses) : null
      }
    }

    // Imagen existente
    if (inventario?.imagen_url) {
      previewUrl.value = inventario.imagen_url
    }

  } catch (err: any) {
    error.value = err.message || 'Error al cargar los datos'
    toast.error(error.value)
  } finally {
    loading.value = false
  }
}

function onImagen(file: File | null) {
  nuevaImagen = file ?? null
  if (previewUrl.value && !previewUrl.value.startsWith('http')) {
    URL.revokeObjectURL(previewUrl.value)
  }
  previewUrl.value = file ? URL.createObjectURL(file) : null
}

async function guardar() {
  if (!props.inventarioId) return

  saving.value = true
  error.value = null

  try {
    const payload: UpdateInventarioPayload = {
      ...form.value,
      ...(isEquipo.value ? { detalle_equipo: detalle_equipo.value } : {}),
      ...(isProducto.value ? { detalle_producto: detalle_producto.value } : {}),
      ...(isRepuesto.value ? { detalle_repuesto: detalle_repuesto.value } : {})
    }

    await updateInventario(props.inventarioId, payload, nuevaImagen || undefined)
    // ❌ REMOVIDO: toast.success('Producto actualizado exitosamente')
    emit('updated') // ✅ Solo emitir el evento
  } catch (err: any) {
    console.error('Error actualizando:', err)
    error.value = err.message || 'Error al actualizar el producto'
    toast.error(error.value)
  } finally {
    saving.value = false
  }
}

// Watchers
watch(() => props.open, (isOpen) => {
  if (isOpen && props.inventarioId) {
    cargarDatos()
  } else {
    // Reset
    nuevaImagen = null
    if (previewUrl.value && !previewUrl.value.startsWith('http')) {
      URL.revokeObjectURL(previewUrl.value)
    }
    previewUrl.value = null
  }
})
</script>

<style scoped>
.modal-enter-active, .modal-leave-active {
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(-20px);
}

.slide-fade-enter-active {
  transition: all 0.3s cubic-bezier(0.55, 0.055, 0.675, 0.19);
}
.slide-fade-leave-active {
  transition: all 0.2s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}
.slide-fade-enter-from {
  transform: translateX(20px);
  opacity: 0;
}
.slide-fade-leave-to {
  transform: translateX(-20px);
  opacity: 0;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>