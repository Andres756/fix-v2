<template>
  <div class="p-6 hover:bg-gray-50 transition-colors">
    <!-- Header del Equipo -->
    <div class="flex items-start justify-between mb-4">
      <div class="flex-1">
        <div class="flex items-center gap-3 mb-2">
          <h4 class="font-semibold text-gray-900">{{ equipo.marca }} {{ equipo.modelo }}</h4>
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
            {{ equipo.comision.tipo === 'porcentaje' ? `${equipo.comision.valor}%` : `$${equipo.comision.valor}` }}
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
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
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
    </div>

    <!-- Lista de Tareas (Expandible) -->
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
</template>

<script setup lang="ts">
import { ref } from 'vue'
import EstadoBadge from './EstadoBadge.vue'
import TareaItem from './TareaItem.vue'
import type { EquipoAsignado } from '../types/tecnico'

const props = defineProps<{
  equipo: EquipoAsignado
  tecnicoId: number
}>()

const emit = defineEmits<{
  'tarea-updated': []
}>()

const showTareas = ref(false)

const toggleTareas = () => {
  showTareas.value = !showTareas.value
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('es-ES')
}
</script>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease-in-out;
}

.slide-down-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>