<template>
  <div class="space-y-4">
    <!-- Barra de progreso general -->
    <div class="flex items-center justify-between mb-4">
      <span class="text-sm font-medium text-gray-700">Progreso General</span>
      <span class="text-sm text-gray-500">{{ completedPercentage }}% completado</span>
    </div>
    
    <div class="w-full bg-gray-200 rounded-full h-3">
      <div 
        class="bg-green-500 h-3 rounded-full transition-all duration-500"
        :style="{ width: `${completedPercentage}%` }"
      ></div>
    </div>

    <!-- Desglose de estados -->
    <div class="grid grid-cols-3 gap-4 mt-6">
      <!-- Pendientes -->
      <div class="text-center">
        <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-2">
          <span class="text-red-600 font-bold text-lg">{{ stats?.tareas_pendientes || 0 }}</span>
        </div>
        <p class="text-xs text-gray-600">Pendientes</p>
      </div>

      <!-- En Proceso -->
      <div class="text-center">
        <div class="w-16 h-16 mx-auto bg-yellow-100 rounded-full flex items-center justify-center mb-2">
          <span class="text-yellow-600 font-bold text-lg">{{ stats?.tareas_en_proceso || 0 }}</span>
        </div>
        <p class="text-xs text-gray-600">En Proceso</p>
      </div>

      <!-- Completadas -->
      <div class="text-center">
        <div class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-2">
          <span class="text-green-600 font-bold text-lg">{{ stats?.tareas_completadas || 0 }}</span>
        </div>
        <p class="text-xs text-gray-600">Completadas</p>
      </div>
    </div>

    <!-- Estadísticas adicionales -->
    <div class="flex justify-between text-sm text-gray-500 pt-4 border-t">
      <span>Total: {{ stats?.total_tareas || 0 }} tareas</span>
      <span>Eficiencia: {{ efficiencyPercentage }}%</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  stats?: {
    total_tareas: number
    tareas_pendientes: number
    tareas_en_proceso: number
    tareas_completadas: number
  }
}>()

const completedPercentage = computed(() => {
  const total = props.stats?.total_tareas || 0
  const completed = props.stats?.tareas_completadas || 0
  return total > 0 ? Math.round((completed / total) * 100) : 0
})

const efficiencyPercentage = computed(() => {
  const total = props.stats?.total_tareas || 0
  const inProgress = props.stats?.tareas_en_proceso || 0
  const completed = props.stats?.tareas_completadas || 0
  const productive = inProgress + completed
  return total > 0 ? Math.round((productive / total) * 100) : 0
})
</script>