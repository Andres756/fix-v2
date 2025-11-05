<template>
  <div class="p-6 hover:bg-gray-50 transition-colors">
    <!-- Header del Equipo -->
    <div class="flex items-start justify-between mb-4">
      <div class="flex-1">
        <div class="flex items-center gap-3 mb-2">
          <h4 class="font-semibold text-gray-900">
            {{ equipo.marca }} {{ equipo.modelo }}
          </h4>
          <EstadoBadge :estado="equipo.estado" />
        </div>

        <div class="text-sm text-gray-600 space-y-1">
          <p><span class="font-medium">IMEI:</span> {{ equipo.imei_serial }}</p>
          <p><span class="font-medium">Cliente:</span> {{ equipo.cliente }}</p>
          <p><span class="font-medium">Orden:</span> {{ equipo.orden_codigo }}</p>
          <p v-if="equipo.fecha_estimada_entrega">
            <span class="font-medium">Entrega estimada:</span>
            {{ formatDate(equipo.fecha_estimada_entrega) }}
          </p>
        </div>
      </div>

      <!-- Comisión (si está habilitada) -->
      <div v-if="equipo.comision.habilitada" class="text-right">
        <div class="bg-green-50 rounded-lg p-3 border border-green-200">
          <p class="text-xs text-green-600 font-medium mb-1">Comisión</p>
          <p class="text-sm font-semibold text-green-700">
            {{ equipo.comision.tipo === 'porcentaje'
              ? `${equipo.comision.valor}%`
              : `$${equipo.comision.valor}` }}
          </p>
          <p class="text-xs text-green-600">
            ~${{ equipo.comision.ganancia_estimada.toFixed(2) }}
          </p>
        </div>
      </div>
    </div>

    <!-- Resumen de Tareas -->
    <div class="bg-gray-50 rounded-lg p-4 mb-4">
      <div class="flex items-center justify-between mb-3">
        <h5 class="font-medium text-gray-900">Resumen de Tareas</h5>
        <button
          @click="toggleTareas"
          class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center gap-1"
        >
          {{ showTareas ? 'Ocultar' : 'Ver' }} tareas
          <svg
            class="w-4 h-4 transition-transform"
            :class="{ 'rotate-180': showTareas }"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M19 9l-7 7-7-7"
            />
          </svg>
        </button>
      </div>

      <div class="grid grid-cols-4 gap-4 text-center">
        <div>
          <div class="text-lg font-bold text-gray-900">{{ equipo.resumen_tareas.total }}</div>
          <div class="text-xs text-gray-500">Total</div>
        </div>
        <div>
          <div class="text-lg font-bold text-red-600">{{ equipo.resumen_tareas.pendientes }}</div>
          <div class="text-xs text-gray-500">Pendientes</div>
        </div>
        <div>
          <div class="text-lg font-bold text-yellow-600">{{ equipo.resumen_tareas.en_proceso }}</div>
          <div class="text-xs text-gray-500">En Proceso</div>
        </div>
        <div>
          <div class="text-lg font-bold text-green-600">{{ equipo.resumen_tareas.completadas }}</div>
          <div class="text-xs text-gray-500">Completadas</div>
        </div>
      </div>

      <!-- ✅ Botón Finalizar Equipo -->
      <div class="mt-5 text-right">
        <button
          v-if="equipo.estado !== 'finalizado'"
          @click="abrirModalConfirmar"
          :disabled="isUpdating"
          class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:bg-green-400 transition-colors font-medium text-sm flex items-center gap-2 ml-auto"
        >
          <svg
            v-if="isUpdating"
            class="animate-spin h-4 w-4 text-white"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"
            />
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            />
          </svg>
          {{ isUpdating ? 'Procesando...' : 'Marcar como Finalizado' }}
        </button>

        <span
          v-else
          class="text-green-600 font-semibold text-sm inline-flex items-center"
        >
          <svg
            class="w-4 h-4 mr-1 text-green-500"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M5 13l4 4L19 7"
            />
          </svg>
          Finalizado
        </span>
      </div>
    </div>

    <!-- Lista de Tareas -->
    <transition name="slide-down">
      <div v-if="showTareas" class="space-y-3">
        <TareaItem
          v-for="tarea in equipo.tareas"
          :key="tarea.id"
          :tarea="tarea"
          :tecnico-id="tecnicoId"
          @updated="$emit('tarea-updated')"
        />
        <div v-if="!equipo.tareas.length" class="text-center py-4 text-gray-500">
          No hay tareas asignadas
        </div>
      </div>
    </transition>
  </div>

  <!-- Modal Confirmar Finalización -->
  <transition name="fade" enter-active-class="transition-opacity duration-200" leave-active-class="transition-opacity duration-150">
    <div v-if="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 relative animate-fade-in">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirmar Finalización</h3>
        <p class="text-sm text-gray-600 mb-6">
          ¿Deseas marcar el equipo
          <span class="font-semibold text-gray-900">{{ equipo.marca }} {{ equipo.modelo }}</span>
          como <span class="text-green-600 font-semibold">FINALIZADO</span>?
          <br />
          <span class="text-yellow-600 text-xs block mt-2">⚠️ Esta acción no se puede deshacer.</span>
        </p>

        <div class="flex justify-end gap-3">
          <button
            @click="showConfirmModal = false"
            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium text-sm"
          >
            Cancelar
          </button>
          <button
            @click="finalizarEquipo"
            :disabled="isUpdating"
            class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:bg-green-400 transition-colors font-medium text-sm flex items-center gap-2"
          >
            <svg v-if="isUpdating" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
            </svg>
            {{ isUpdating ? 'Procesando...' : 'Confirmar' }}
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { toast } from 'vue3-toastify'
import EstadoBadge from './EstadoBadge.vue'
import TareaItem from './TareaItem.vue'
import { actualizarEstadoEquipo } from '../api/tecnicos'
import type { EquipoAsignado } from '../types/tecnico'

const props = defineProps<{ equipo: EquipoAsignado; tecnicoId: number }>()
const emit = defineEmits<{ 'tarea-updated': [] }>()

const showTareas = ref(false)
const showConfirmModal = ref(false)
const isUpdating = ref(false)

const toggleTareas = () => (showTareas.value = !showTareas.value)
const formatDate = (date: string) => new Date(date).toLocaleDateString('es-ES')
const abrirModalConfirmar = () => (showConfirmModal.value = true)

// ✅ Actualiza estado del equipo con el nuevo endpoint corto
async function finalizarEquipo() {
  try {
    isUpdating.value = true
    console.log('🔍 Enviando PATCH /equipos/' + props.equipo.id)

    await actualizarEstadoEquipo(props.equipo.id, 'finalizado')
    toast.success(`Equipo ${props.equipo.modelo} marcado como finalizado`)
    emit('tarea-updated')
    showConfirmModal.value = false
  } catch (e: any) {
    console.error('❌ Error actualizando equipo:', e)
    const msg = e?.response?.data?.message || 'No se pudo finalizar el equipo'
    toast.error(msg)
  } finally {
    isUpdating.value = false
  }
}
</script>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease-in-out;
}
.slide-down-enter-from,
.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
