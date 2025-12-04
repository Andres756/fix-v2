<template>
  <Teleport to="body">
    <Transition name="modal-fade">
      <div
        v-if="isOpen"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[60] p-4"
        @click.self="handleClose"
      >
        <Transition name="modal-slide">
          <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-900">
                Cambiar Estado de Entrada
              </h3>
              <p class="text-sm text-gray-500 mt-1">
                Entrada #{{ entrada?.id }}
              </p>
            </div>

            <!-- Body -->
            <div class="px-6 py-4 space-y-4">
              <!-- Estado Actual -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Estado Actual
                </label>
                <div v-if="entrada?.estado_entrada" class="flex items-center gap-2">
                  <span
                    :style="{ backgroundColor: entrada.estado_entrada.color || '#6B7280' }"
                    class="px-3 py-1 rounded-full text-white text-sm font-medium"
                  >
                    {{ entrada.estado_entrada.nombre }}
                  </span>
                </div>
                <div v-else class="text-sm text-gray-500 italic">
                  Sin estado asignado
                </div>
              </div>

              <!-- Nuevo Estado -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Nuevo Estado *
                </label>
                <select
                  v-model="nuevoEstadoId"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  :disabled="loading"
                >
                  <option :value="null">Selecciona un estado</option>
                  <option
                    v-for="estado in estadosDisponibles"
                    :key="estado.id"
                    :value="estado.id"
                  >
                    {{ estado.nombre }}
                  </option>
                </select>
              </div>

              <!-- Preview del nuevo estado con descripción -->
              <div v-if="estadoSeleccionado" class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-xs text-gray-600 mb-2">Vista previa:</p>
                <span
                  :style="{ backgroundColor: estadoSeleccionado.color }"
                  class="inline-block px-3 py-1 rounded-full text-white text-sm font-medium mb-2"
                >
                  {{ estadoSeleccionado.nombre }}
                </span>
                <p class="text-xs text-gray-700 mb-2">
                  {{ estadoSeleccionado.descripcion }}
                </p>
                
                <!-- Información sobre impacto -->
                <div class="mt-2 p-2 bg-blue-50 rounded text-xs text-blue-800">
                  <p class="font-medium mb-1">Impacto del cambio:</p>
                  <ul class="list-disc list-inside space-y-1">
                    <li v-if="estadoSeleccionado.codigo === 'completada'">
                      ✅ Inventario ingresa
                    </li>
                    <li v-if="estadoSeleccionado.codigo === 'completada'">
                      💰 Gasto registrado
                    </li>
                    
                    <li v-if="estadoSeleccionado.codigo === 'en_transito'">
                      ❌ Inventario NO ingresa
                    </li>
                    <li v-if="estadoSeleccionado.codigo === 'en_transito'">
                      💰 Gasto registrado (deuda)
                    </li>
                    
                    <li v-if="estadoSeleccionado.codigo === 'pendiente_pago'">
                      ✅ Inventario ingresa
                    </li>
                    <li v-if="estadoSeleccionado.codigo === 'pendiente_pago'">
                      ⏳ Gasto pendiente
                    </li>
                    
                    <li v-if="estadoSeleccionado.codigo === 'parcial'">
                      ⚠️ Requiere indicar qué llegó
                    </li>
                  </ul>
                </div>
              </div>

              <!-- Observaciones -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Observaciones (opcional)
                </label>
                <textarea
                  v-model="observaciones"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  placeholder="Motivo del cambio de estado..."
                  :disabled="loading"
                ></textarea>
              </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
              <button
                @click="handleClose"
                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                :disabled="loading"
              >
                Cancelar
              </button>
              <button
                @click="cambiarEstado"
                :disabled="!nuevoEstadoId || loading"
                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ loading ? 'Cambiando...' : 'Cambiar Estado' }}
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { toast } from 'vue3-toastify'
import http from '../../../shared/api/http'
import type { EntradaInventario } from '../types/inventoryEntry'

interface EstadoEntrada {
  id: number
  nombre: string
  codigo: string
  color: string
  descripcion?: string
}

const props = defineProps<{
  isOpen: boolean
  entrada: EntradaInventario | null
}>()

const emit = defineEmits<{
  close: []
  success: []
}>()

// Estado
const loading = ref(false)
const nuevoEstadoId = ref<number | null>(null)
const observaciones = ref('')
const estados = ref<EstadoEntrada[]>([])

// Computed
const estadosDisponibles = computed(() => {
  return estados.value.filter(e => e.id !== props.entrada?.estado_entrada_id)
})

const estadoSeleccionado = computed(() => {
  return estados.value.find(e => e.id === nuevoEstadoId.value)
})

// Métodos
const handleClose = () => {
  if (!loading.value) {
    nuevoEstadoId.value = null
    observaciones.value = ''
    emit('close')
  }
}

const cargarEstados = async () => {
  try {
    const { data } = await http.get('/parametros/estados-entrada/options')
    estados.value = data || []
  } catch (error) {
    console.error('Error cargando estados:', error)
    toast.error('No se pudieron cargar los estados')
  }
}

const cambiarEstado = async () => {
  if (!props.entrada || !nuevoEstadoId.value) return

  try {
    loading.value = true
    
    await http.patch(`/inventario/entradas-producto/${props.entrada.id}/estado`, {
      estado_entrada_id: nuevoEstadoId.value,
      observaciones: observaciones.value || undefined
    })

    const estadoNuevo = estadoSeleccionado.value
    toast.success(`Estado cambiado a "${estadoNuevo?.nombre}"`)
    
    emit('success')
    handleClose()
  } catch (error: any) {
    console.error('Error cambiando estado:', error)
    toast.error(error?.response?.data?.message || 'No se pudo cambiar el estado')
  } finally {
    loading.value = false
  }
}

// Lifecycle
watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    cargarEstados()
  }
})
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.modal-slide-enter-active {
  transition: all 0.25s ease-out;
}

.modal-slide-leave-active {
  transition: all 0.2s ease-in;
}

.modal-slide-enter-from {
  opacity: 0;
  transform: scale(0.95) translateY(-20px);
}

.modal-slide-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(20px);
}
</style>