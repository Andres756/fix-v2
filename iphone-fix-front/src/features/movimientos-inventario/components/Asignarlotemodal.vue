<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[60] p-4"
      @click.self="handleClose"
    >
      <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-xl font-semibold text-gray-900">
            {{ entrada?.lote_id ? 'Editar Distribución de Flete' : 'Asignar Lote a Entrada' }}
          </h2>
          <p class="text-sm text-gray-500 mt-1">
            Entrada #{{ entrada?.id }} - {{ formatDate(entrada?.fecha_entrada || '') }}
          </p>
        </div>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto px-6 py-4">
          <!-- Selección de lote -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Seleccionar Lote
              <span class="text-red-500">*</span>
            </label>

            <div class="flex gap-2">
              <select
                v-model="form.lote_id"
                @change="handleLoteChange"
                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :disabled="loading"
              >
                <option :value="null">Seleccione un lote</option>
                <option
                  v-for="lote in lotes"
                  :key="lote.id"
                  :value="lote.id"
                >
                  {{ lote.numero_lote }} - Flete: {{ formatCurrency(lote.costo_flete || 0) }}
                </option>
              </select>

              <button
                @click="modalCrearLote = true"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors whitespace-nowrap"
              >
                + Nuevo Lote
              </button>
            </div>

            <div v-if="loteSeleccionado" class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
              <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                  <span class="text-gray-600">Proveedor:</span>
                  <p class="font-medium text-gray-900">{{ loteSeleccionado.proveedor?.nombre || 'N/A' }}</p>
                </div>
                <div>
                  <span class="text-gray-600">Costo Flete:</span>
                  <p class="font-medium text-gray-900">{{ formatCurrency(loteSeleccionado.costo_flete || 0) }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Distribución del flete -->
          <div v-if="form.lote_id && loteSeleccionado">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-base font-semibold text-gray-900">
                Distribución del Flete
              </h3>
              
              <button
                @click="distribuirAutomaticamente"
                class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
              >
                Distribuir Automáticamente
              </button>
            </div>

            <div class="space-y-3">
              <div
                v-for="(item, index) in form.distribucion_flete"
                :key="item.item_id"
                class="p-4 border border-gray-200 rounded-lg"
              >
                <div class="flex items-start justify-between gap-4">
                  <div class="flex-1">
                    <p class="font-medium text-gray-900">
                      {{ getItemInfo(item.item_id)?.inventario?.nombre }}
                    </p>
                    <p class="text-sm text-gray-500">
                      {{ getItemInfo(item.item_id)?.inventario?.codigo }}
                    </p>
                    <p class="text-sm text-gray-600 mt-1">
                      Cantidad: {{ getItemInfo(item.item_id)?.cantidad }} x 
                      {{ formatCurrency(getItemInfo(item.item_id)?.costo_unitario || 0) }}
                    </p>
                  </div>

                  <div class="w-48">
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                      Flete Asignado
                    </label>
                    <input
                      v-model.number="item.costo_flete_asignado"
                      type="number"
                      step="0.01"
                      min="0"
                      :max="loteSeleccionado.costo_flete || 0"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      @input="validarDistribucion"
                    />
                  </div>

                  <div class="w-48">
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                      Total Item
                    </label>
                    <input
                      :value="formatCurrency(calcularTotalItem(item))"
                      type="text"
                      disabled
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 font-medium"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Resumen de distribución -->
            <div class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
              <div class="grid grid-cols-3 gap-4 text-sm">
                <div>
                  <span class="text-gray-600">Flete Total:</span>
                  <p class="text-lg font-semibold text-gray-900">
                    {{ formatCurrency(loteSeleccionado.costo_flete || 0) }}
                  </p>
                </div>
                <div>
                  <span class="text-gray-600">Flete Distribuido:</span>
                  <p class="text-lg font-semibold" :class="fleteDistribuidoClass">
                    {{ formatCurrency(fleteDistribuido) }}
                  </p>
                </div>
                <div>
                  <span class="text-gray-600">Flete Pendiente:</span>
                  <p class="text-lg font-semibold" :class="fletePendienteClass">
                    {{ formatCurrency(fletePendiente) }}
                  </p>
                </div>
              </div>

              <div v-if="fletePendiente !== 0" class="mt-3 text-sm">
                <p v-if="fletePendiente > 0" class="text-yellow-700">
                  ⚠️ Aún falta distribuir {{ formatCurrency(fletePendiente) }} del flete
                </p>
                <p v-else class="text-red-700">
                  ❌ Se ha excedido el flete total en {{ formatCurrency(Math.abs(fletePendiente)) }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
          <button
            @click="handleClose"
            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors"
            :disabled="saving"
          >
            Cancelar
          </button>
          <button
            @click="guardar"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="!puedeGuardar || saving"
          >
            {{ saving ? 'Guardando...' : 'Guardar' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Modal crear lote -->
    <CrearLoteModal
      v-if="modalCrearLote"
      :is-open="modalCrearLote"
      :proveedor-id="entrada?.proveedor_id || null"
      @close="modalCrearLote = false"
      @success="handleLoteCreado"
    />
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { toast } from 'vue3-toastify'
import { 
  fetchLotesOptions, 
  asignarLoteEntrada 
} from '../../inventario/api/inventoryEntries'
import type { 
  EntradaInventario, 
  EntradaInventarioItem,
  Lote,
  AsignarLotePayload 
} from '../../inventario/types/inventoryEntry'
import CrearLoteModal from '../../movimientos-inventario/components/CrearLoteModal.vue'

// Props
interface Props {
  isOpen: boolean
  entrada: EntradaInventario | null
}

const props = defineProps<Props>()

// Emits
const emit = defineEmits<{
  close: []
  success: []
}>()

// Estado
const loading = ref(false)
const saving = ref(false)
const lotes = ref<Lote[]>([])
const modalCrearLote = ref(false)

const form = ref<{
  lote_id: number | null
  distribucion_flete: {
    item_id: number
    costo_flete_asignado: number
  }[]
}>({
  lote_id: null,
  distribucion_flete: [],
})

// Computed
const loteSeleccionado = computed(() => {
  if (!form.value.lote_id) return null
  return lotes.value.find(l => l.id === form.value.lote_id) || null
})

const fleteDistribuido = computed(() => {
  return form.value.distribucion_flete.reduce((sum, item) => {
    return sum + (item.costo_flete_asignado || 0)
  }, 0)
})

const fletePendiente = computed(() => {
  const fleteTotal = loteSeleccionado.value?.costo_flete || 0
  return fleteTotal - fleteDistribuido.value
})

const fleteDistribuidoClass = computed(() => {
  if (fleteDistribuido.value === 0) return 'text-gray-900'
  if (fletePendiente.value === 0) return 'text-green-700'
  return 'text-blue-700'
})

const fletePendienteClass = computed(() => {
  if (fletePendiente.value === 0) return 'text-green-700'
  if (fletePendiente.value > 0) return 'text-yellow-700'
  return 'text-red-700'
})

const puedeGuardar = computed(() => {
  return form.value.lote_id !== null && fletePendiente.value === 0
})

// Métodos
const handleClose = () => {
  emit('close')
}

const cargarLotes = async () => {
  loading.value = true
  try {
    lotes.value = await fetchLotesOptions(props.entrada?.proveedor_id || undefined)
  } catch (error: any) {
    console.error('Error cargando lotes:', error)
    toast.error('Error al cargar los lotes')
  } finally {
    loading.value = false
  }
}

const handleLoteChange = () => {
  inicializarDistribucion()
}

const inicializarDistribucion = () => {
  if (!props.entrada?.items) return

  form.value.distribucion_flete = props.entrada.items.map(item => ({
    item_id: item.id,
    costo_flete_asignado: item.costo_flete_asignado || 0,
  }))
}

const distribuirAutomaticamente = () => {
  if (!loteSeleccionado.value || !props.entrada?.items) return

  const fleteTotal = loteSeleccionado.value.costo_flete || 0
  const items = props.entrada.items
  
  // Calcular el peso de cada item basado en su subtotal
  const subtotales = items.map(item => item.costo_unitario * item.cantidad)
  const totalSubtotales = subtotales.reduce((sum, st) => sum + st, 0)
  
  if (totalSubtotales === 0) {
    // Si no hay subtotales, distribuir equitativamente
    const fletePorItem = fleteTotal / items.length
    form.value.distribucion_flete = items.map(item => ({
      item_id: item.id,
      costo_flete_asignado: parseFloat(fletePorItem.toFixed(2)),
    }))
  } else {
    // Distribuir proporcionalmente al subtotal
    let fleteDistribuido = 0
    
    form.value.distribucion_flete = items.map((item, index) => {
      const subtotal = item.costo_unitario * item.cantidad
      const proporcion = subtotal / totalSubtotales
      let fleteAsignado = fleteTotal * proporcion
      
      // En el último item, ajustar para que sume exactamente el flete total
      if (index === items.length - 1) {
        fleteAsignado = fleteTotal - fleteDistribuido
      } else {
        fleteAsignado = parseFloat(fleteAsignado.toFixed(2))
        fleteDistribuido += fleteAsignado
      }
      
      return {
        item_id: item.id,
        costo_flete_asignado: fleteAsignado,
      }
    })
  }
  
  toast.success('Flete distribuido automáticamente')
}

const validarDistribucion = () => {
  // Validar que no se exceda el flete total
  const fleteTotal = loteSeleccionado.value?.costo_flete || 0
  const distribuido = fleteDistribuido.value
  
  if (distribuido > fleteTotal) {
    toast.warning('El flete distribuido excede el flete total del lote')
  }
}

const guardar = async () => {
  if (!puedeGuardar.value || !props.entrada) return

  saving.value = true
  try {
    const payload: AsignarLotePayload = {
      lote_id: form.value.lote_id!,
      distribucion_flete: form.value.distribucion_flete,
    }

    await asignarLoteEntrada(props.entrada.id, payload)
    
    toast.success('Lote asignado y flete distribuido correctamente')
    emit('success')
  } catch (error: any) {
    console.error('Error asignando lote:', error)
    toast.error(error?.response?.data?.message || 'Error al asignar el lote')
  } finally {
    saving.value = false
  }
}

const getItemInfo = (itemId: number): EntradaInventarioItem | undefined => {
  return props.entrada?.items?.find(item => item.id === itemId)
}

const calcularTotalItem = (distribucionItem: { item_id: number; costo_flete_asignado: number }) => {
  const item = getItemInfo(distribucionItem.item_id)
  if (!item) return 0
  
  const subtotal = item.costo_unitario * item.cantidad
  return subtotal + (distribucionItem.costo_flete_asignado || 0)
}

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

const handleLoteCreado = (nuevoLote: Lote) => {
  modalCrearLote.value = false
  lotes.value.push(nuevoLote)
  form.value.lote_id = nuevoLote.id
  inicializarDistribucion()
  toast.success('Lote creado correctamente')
}

// Lifecycle
onMounted(() => {
  cargarLotes()
  
  // Si la entrada ya tiene lote, pre-cargar la distribución
  if (props.entrada?.lote_id) {
    form.value.lote_id = props.entrada.lote_id
    inicializarDistribucion()
  }
})

watch(() => props.entrada, (newVal) => {
  if (newVal?.lote_id) {
    form.value.lote_id = newVal.lote_id
    inicializarDistribucion()
  }
}, { deep: true })
</script>