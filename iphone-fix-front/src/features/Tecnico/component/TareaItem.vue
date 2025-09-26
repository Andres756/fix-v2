<template>
  <div class="bg-white border rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between">
      <!-- Información de la Tarea -->
      <div class="flex-1">
        <div class="flex items-center gap-3 mb-3">
          <h6 class="font-medium text-gray-900">{{ tarea.nombre }}</h6>
          <EstadoBadge :estado="tarea.estado" size="sm" />
        </div>
        
        <!-- Selector de Estado Moderno -->
        <div class="flex items-center gap-3">
          <span class="text-sm text-gray-600 font-medium">Estado:</span>
          
          <select
            v-model="estadoLocal"
            @change="cambiarEstado"
            :disabled="updating"
            class="px-3 py-1.5 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white"
            :class="{
              'opacity-50 cursor-not-allowed': updating
            }"
          >
            <option value="pendiente">🔴 Pendiente</option>
            <option value="en_proceso">🟡 En Proceso</option>
            <option value="completada">🟢 Completada</option>
            <option value="cancelada">⚫ Cancelada</option>
          </select>

          <!-- Loading Indicator -->
          <div v-if="updating" class="flex items-center text-blue-600">
            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
            </svg>
            <span class="ml-1 text-xs">Actualizando...</span>
          </div>

          <!-- Confirmación Visual -->
          <transition name="fade">
            <div v-if="showSuccess" class="flex items-center text-green-600">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span class="ml-1 text-xs">¡Actualizado!</span>
            </div>
          </transition>
        </div>
      </div>

      <!-- Acciones -->
      <div class="flex items-center gap-2 ml-4">
        <!-- Ver Historial -->
        <button
          @click="toggleHistorial"
          class="p-2 text-gray-400 hover:text-gray-600 transition-colors rounded-lg hover:bg-gray-50"
          title="Ver historial"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Historial (Expandible) -->
    <transition name="slide-down">
      <div v-if="showHistorial" class="mt-4 pt-4 border-t border-gray-200">
        <h6 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
          <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <span>Historial de cambios</span>
          <div class="h-px bg-gray-200 flex-1 ml-2"></div>
        </h6>
        
        <div v-if="loading" class="text-center py-4 text-gray-500">
          <div class="animate-spin mx-auto w-6 h-6 border-2 border-blue-600 border-t-transparent rounded-full"></div>
          <p class="mt-2 text-sm">Cargando historial...</p>
        </div>
        
        <div v-else-if="historial.length" class="space-y-3 max-h-40 overflow-y-auto">
          <div
            v-for="item in historial"
            :key="item.id"
            class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 border-l-4 border-blue-400 shadow-sm hover:shadow-md transition-shadow"
          >
            <div class="flex justify-between items-start mb-2">
              <div class="flex items-center gap-3">
                <!-- Estado Anterior -->
                <div class="flex items-center gap-2">
                  <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                        :class="getEstadoBadgeClasses(item.estado_anterior)">
                    {{ getEstadoIcon(item.estado_anterior) }} {{ formatEstado(item.estado_anterior) }}
                  </span>
                </div>
                
                <!-- Flecha de transición -->
                <div class="flex items-center">
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                  </svg>
                </div>
                
                <!-- Estado Nuevo -->
                <div class="flex items-center gap-2">
                  <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                        :class="getEstadoBadgeClasses(item.estado_nuevo)">
                    {{ getEstadoIcon(item.estado_nuevo) }} {{ formatEstado(item.estado_nuevo) }}
                  </span>
                </div>
              </div>
              
              <!-- Fecha y hora -->
              <div class="text-right">
                <div class="text-xs text-gray-500 font-medium">{{ formatDate(item.cambiado_en) }}</div>
              </div>
            </div>
            
            <!-- Información del técnico -->
            <div class="flex items-center gap-2 mt-2 pt-2 border-t border-blue-100">
              <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <span class="text-xs text-gray-600">
                Cambio realizado por <strong class="text-gray-800">{{ item.tecnico.nombre }}</strong>
              </span>
            </div>
          </div>
        </div>
        
        <div v-else class="text-center py-6 bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg border-2 border-dashed border-gray-200">
          <div class="flex flex-col items-center">
            <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mb-3">
              <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <p class="text-sm text-gray-500 font-medium">Sin historial de cambios</p>
            <p class="text-xs text-gray-400 mt-1">Los cambios de estado aparecerán aquí</p>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import EstadoBadge from './EstadoBadge.vue'
import { updateTareaEstado, fetchHistorialTarea } from '../api/tecnicos'
import type { Tarea, HistorialTarea } from '../types/tecnico'

const props = defineProps<{
  tarea: Tarea
  tecnicoId: number
}>()

const emit = defineEmits<{
  updated: []
}>()

const updating = ref(false)
const showHistorial = ref(false)
const loading = ref(false)
const historial = ref<HistorialTarea[]>([])
const showSuccess = ref(false)

// Estado local para el select
const estadoLocal = ref(props.tarea.estado)

// Sincronizar con props cuando cambie
watch(() => props.tarea.estado, (newEstado) => {
  estadoLocal.value = newEstado
}, { immediate: true })

async function cambiarEstado() {
  const nuevoEstado = estadoLocal.value
  
  if (updating.value || props.tarea.estado === nuevoEstado) {
    return
  }

  updating.value = true
  try {
    await updateTareaEstado(props.tecnicoId, props.tarea.id, nuevoEstado)
    
    // Mostrar confirmación visual
    showSuccess.value = true
    setTimeout(() => {
      showSuccess.value = false
    }, 2000)
    
    // Emitir evento para actualizar el componente padre (SIN recargar página)
    emit('updated')
    
    // Recargar historial si está visible
    if (showHistorial.value) {
      await loadHistorial()
    }
  } catch (error) {
    console.error('Error actualizando estado:', error)
    
    // Revertir el select al estado anterior
    estadoLocal.value = props.tarea.estado
    
    // Mostrar error
    alert('Error al actualizar el estado de la tarea')
  } finally {
    updating.value = false
  }
}

async function toggleHistorial() {
  showHistorial.value = !showHistorial.value
  
  if (showHistorial.value && !historial.value.length) {
    await loadHistorial()
  }
}

async function loadHistorial() {
  loading.value = true
  try {
    console.log('🔍 Cargando historial para:', {
      tecnicoId: props.tecnicoId,
      tareaId: props.tarea.id
    })
    
    historial.value = await fetchHistorialTarea(props.tecnicoId, props.tarea.id)
    
    console.log('✅ Historial cargado:', historial.value)
  } catch (error) {
    console.error('❌ Error cargando historial:', error)
  } finally {
    loading.value = false
  }
}

function formatEstado(estado: string | null) {
  if (!estado) return 'Inicial'
  const estados = {
    'pendiente': 'Pendiente',
    'en_proceso': 'En Proceso', 
    'completada': 'Completada',
    'cancelada': 'Cancelada'
  }
  return estados[estado as keyof typeof estados] || estado
}

function formatDate(date: string) {
  const dateObj = new Date(date)
  const now = new Date()
  const diffMs = now.getTime() - dateObj.getTime()
  const diffMins = Math.floor(diffMs / (1000 * 60))
  const diffHours = Math.floor(diffMs / (1000 * 60 * 60))
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24))

  // Mostrar tiempo relativo si es reciente
  if (diffMins < 1) return 'Ahora mismo'
  if (diffMins < 60) return `Hace ${diffMins} min`
  if (diffHours < 24) return `Hace ${diffHours}h`
  if (diffDays < 7) return `Hace ${diffDays} días`
  
  // Para fechas más antiguas, mostrar fecha formateada
  return dateObj.toLocaleString('es-ES', {
    day: '2-digit',
    month: '2-digit',
    year: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}

function getEstadoIcon(estado: string | null) {
  const icons = {
    'pendiente': '🔴',
    'en_proceso': '🟡', 
    'completada': '🟢',
    'cancelada': '⚫'
  }
  return icons[estado as keyof typeof icons] || '⚪'
}

function getEstadoBadgeClasses(estado: string | null) {
  const classes = {
    'pendiente': 'bg-red-100 text-red-700 border border-red-200',
    'en_proceso': 'bg-yellow-100 text-yellow-700 border border-yellow-200',
    'completada': 'bg-green-100 text-green-700 border border-green-200',
    'cancelada': 'bg-gray-100 text-gray-700 border border-gray-200'
  }
  return classes[estado as keyof typeof classes] || 'bg-gray-100 text-gray-700 border border-gray-200'
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

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>