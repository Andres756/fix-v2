<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-[999] flex items-center justify-center"
      @click.self="handleClose"
    >
      <!-- Fondo con gradiente + blur -->
      <div
        class="absolute inset-0 bg-gradient-to-br from-black/70 via-black/50 to-gray-900/60 backdrop-blur-sm"
      ></div>

      <!-- Contenedor de la Modal -->
      <div
        class="relative bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden animate-fadeIn"
      >
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-xl font-semibold text-gray-900">
            💰 Costos Adicionales - Entrada #{{ entrada?.id }}
          </h2>
          <p class="text-sm text-gray-500 mt-1">
            {{ entrada?.tipo_entrada === 'cliente' ? 'Cliente' : 'Proveedor' }}: 
            {{ entrada?.cliente?.nombre || entrada?.proveedor?.nombre || 'N/A' }}
          </p>
        </div>

        <!-- Tabs -->
        <div class="border-b border-gray-200 px-6">
          <nav class="flex -mb-px space-x-8">
            <button
              v-for="tab in tabs"
              :key="tab.id"
              @click="activeTab = tab.id"
              :class="[
                activeTab === tab.id
                  ? 'border-purple-500 text-purple-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors'
              ]"
            >
              {{ tab.icon }} {{ tab.label }}
            </button>
          </nav>
        </div>

        <!-- Body scrollable -->
        <div class="flex-1 overflow-y-auto p-6">
          <RepuestosInventarioSection
            v-if="activeTab === 'inventario'"
            :entrada-id="entrada!.id"
            @changed="handleCostosChanged"
          />

          <RepuestosExternosSection
            v-if="activeTab === 'externos'"
            :entrada-id="entrada!.id"
            @changed="handleCostosChanged"
          />

          <PagosTecnicosSection
            v-if="activeTab === 'tecnicos'"
            :entrada-id="entrada!.id"
            @changed="handleCostosChanged"
          />
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
          <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-4">
            <div>
              <p class="text-xs text-gray-600">Costo Base</p>
              <p class="text-lg font-semibold text-gray-900">
                {{ formatCurrency(resumen.costo_base) }}
              </p>
            </div>
            <div>
              <p class="text-xs text-gray-600">Rep. Inventario</p>
              <p class="text-lg font-semibold text-blue-600">
                {{ formatCurrency(resumen.repuestos_inventario) }}
              </p>
            </div>
            <div>
              <p class="text-xs text-gray-600">Rep. Externos</p>
              <p class="text-lg font-semibold text-blue-600">
                {{ formatCurrency(resumen.repuestos_externos) }}
              </p>
            </div>
            <div>
              <p class="text-xs text-gray-600">Pagos Técnicos</p>
              <p class="text-lg font-semibold text-purple-600">
                {{ formatCurrency(resumen.total_tecnicos) }}
              </p>
            </div>
            <div class="border-l-2 border-gray-300 pl-4">
              <p class="text-xs text-gray-600">COSTO FINAL</p>
              <p class="text-xl font-bold text-green-600">
                {{ formatCurrency(resumen.costo_total_final) }}
              </p>
            </div>
          </div>

          <div class="flex justify-end">
            <button
              @click="handleClose"
              class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
            >
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: scale(.95); }
  to { opacity: 1; transform: scale(1); }
}
.animate-fadeIn {
  animation: fadeIn .25s ease-out;
}
</style>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import { toast } from 'vue3-toastify'
import { fetchResumenCostos } from '../api/costosAdicionales'
import type { ResumenCostos } from '../api/costosAdicionales'
import type { EntradaInventario } from '../../inventario/types/inventoryEntry'
import RepuestosInventarioSection from './RepuestosInventarioSection.vue'
import RepuestosExternosSection from './RepuestosExternosSection.vue'
import PagosTecnicosSection from './PagosTecnicosSection.vue'

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
const activeTab = ref<'inventario' | 'externos' | 'tecnicos'>('inventario')
const resumen = ref<ResumenCostos>({
  costo_base: 0,
  repuestos_inventario: 0,
  repuestos_externos: 0,
  total_repuestos: 0,
  total_tecnicos: 0,
  costo_total_final: 0,
})

const tabs = [
  { id: 'inventario' as const, label: 'Repuestos Inventario', icon: '📦' },
  { id: 'externos' as const, label: 'Repuestos Externos', icon: '🛒' },
  { id: 'tecnicos' as const, label: 'Pagos Técnicos', icon: '👨‍🔧' },
]

// Métodos
const handleClose = () => {
  emit('close')
}

const cargarResumen = async () => {
  if (!props.entrada) return

  try {
    resumen.value = await fetchResumenCostos(props.entrada.id)
  } catch (error: any) {
    console.error('Error cargando resumen:', error)
  }
}

const handleCostosChanged = () => {
  cargarResumen()
  emit('success') // Para recargar la tabla de entradas
}

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
  }).format(value)
}

// Lifecycle
watch(() => props.isOpen, (isOpen) => {
  if (isOpen && props.entrada) {
    cargarResumen()
  }
})

onMounted(() => {
  if (props.isOpen && props.entrada) {
    cargarResumen()
  }
})
</script>