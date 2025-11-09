<template>
  <div class="space-y-6">
    <!-- Header elegante -->
    <div class="bg-gradient-to-r from-violet-50 to-purple-50 rounded-xl p-5 border border-violet-200">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
          </div>
          <div>
            <h4 class="text-lg font-semibold text-gray-800">Tareas del Equipo</h4>
            <p class="text-sm text-violet-600" v-if="equipoImei">
              IMEI/Serial: <span class="font-medium">{{ equipoImei }}</span>
            </p>
          </div>
        </div>
        <div v-if="totalTareas > 0" class="text-right">
          <p class="text-xs text-gray-500 mb-1">Total acumulado</p>
          <p class="text-lg font-bold text-violet-700">{{ money(totalTareas) }}</p>
        </div>
      </div>
    </div>

    <!-- Form nueva tarea con diseño consistente -->
    <div
      class="bg-white border-2 rounded-xl p-5 transition-all duration-300"
      :class="addHighlight ? 'border-violet-400 shadow-lg shadow-violet-200' : 'border-gray-200 hover:border-violet-300'"
      ref="addBlock"
    >
      <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
          <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
        </div>
        <div>
          <h5 class="text-base font-semibold text-gray-800">Agregar Nueva Tarea</h5>
          <p class="text-sm text-gray-500">Selecciona el tipo de trabajo y ajusta el costo</p>
        </div>
      </div>
      
      <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Tipo de trabajo *</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
              </div>
              <select 
                v-model.number="newTask.tipo_trabajo_id" 
                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                required
              >
                <option :value="0" disabled>Selecciona un tipo de trabajo...</option>
                <option v-for="t in tipos" :key="t.id" :value="t.id" class="py-2">
                  {{ t.nombre }} — {{ money(Number(t.costo_sugerido ?? 0)) }}
                </option>
              </select>
            </div>
          </div>

          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Costo a aplicar *</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500">$</span>
              </div>
              <input
                type="number"
                v-model.number="newTask.costo_aplicado"
                placeholder="0"
                min="0"
                step="1000"
                required
                class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              />
            </div>
          </div>
        </div>
        
        <div class="flex justify-end">
          <button 
            @click="agregar" 
            :disabled="adding || newTask.tipo_trabajo_id===0 || !newTask.costo_aplicado" 
            class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center gap-2 disabled:opacity-50"
          >
            <svg v-if="adding" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
              <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/>
              <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" class="opacity-75"/>
            </svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            {{ adding ? 'Agregando...' : 'Agregar Tarea' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Lista de tareas -->
    <div class="space-y-4">
      <div class="flex items-center justify-between">
        <h5 class="text-base font-semibold text-gray-800">Tareas Registradas</h5>
        <span class="text-sm text-gray-500">{{ tareas.length }} tarea{{ tareas.length !== 1 ? 's' : '' }}</span>
      </div>

      <div v-if="loading" class="flex items-center justify-center py-12">
        <div class="flex items-center space-x-3">
          <div class="animate-spin rounded-full h-6 w-6 border-2 border-violet-600 border-t-transparent"></div>
          <span class="text-gray-600">Cargando tareas...</span>
        </div>
      </div>

      <div v-else-if="tareas.length === 0" class="text-center py-12 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300">
        <div class="mx-auto w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mb-4">
          <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Aún no hay tareas</h3>
        <p class="text-gray-600">Agrega la primera tarea para este equipo</p>
      </div>

      <div v-else class="space-y-3">
        <div 
          v-for="t in tareas" 
          :key="t.id" 
          class="group bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md hover:border-violet-200 transition-all duration-200"
        >
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4 flex-1">
              <!-- Icono de estado -->
              <div class="relative">
                <div 
                  class="w-10 h-10 rounded-xl flex items-center justify-center"
                  :class="estadoIconClass(t.estado)"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path 
                      stroke-linecap="round" 
                      stroke-linejoin="round" 
                      stroke-width="2" 
                      :d="estadoIconPath(t.estado)"
                    />
                  </svg>
                </div>
                <div 
                  class="absolute -top-1 -right-1 w-3 h-3 rounded-full border-2 border-white"
                  :class="estadoDotClass(t.estado)"
                ></div>
              </div>
              
              <!-- Información de la tarea -->
              <div class="flex-1">
                <h6 class="font-semibold text-gray-900 text-base">
                  {{ nombreTrabajo(t.tipo_trabajo_id) }}
                </h6>
                <div class="flex items-center space-x-4 mt-1">
                  <span class="text-lg font-bold text-green-700">
                    {{ money(t.costo_aplicado) }}
                  </span>
                  <span 
                    class="px-3 py-1 text-xs font-medium rounded-full"
                    :class="estadoClass(t.estado)"
                  >
                    {{ estadoLabel(t.estado) }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Botón eliminar -->
            <button 
              class="group-hover:opacity-100 opacity-0 w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200"
              @click="eliminar(t)"
              title="Eliminar tarea"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

import { fetchTareas, createTarea, deleteTarea } from '../api/tareas'
import { fetchTiposTrabajo, type TipoTrabajo } from '../api/tiposTrabajo'
import type { Tarea, CreateTareaPayload } from '../types/tarea'

const props = defineProps<{
  clienteId: number
  ordenId: number
  equipoId: number
  equipoImei?: string
}>()

const emit = defineEmits<{
  (e: 'changed'): void
  (e: 'total-changed', total: number): void
}>()

const tareas = ref<Tarea[]>([])
const tipos  = ref<TipoTrabajo[]>([])
const loading = ref(false)
const adding = ref(false)

const newTask = ref<CreateTareaPayload>({
  tipo_trabajo_id: 0,
  costo_aplicado: null,
})

watch(() => newTask.value.tipo_trabajo_id, id => {
  const trabajo = tipos.value.find(x => x.id === id)
  newTask.value.costo_aplicado = trabajo?.costo_sugerido ?? null
})

const addBlock = ref<HTMLElement | null>(null)
const addHighlight = ref(false)

const totalTareas = computed(() => tareas.value.reduce((acc, t) => acc + Number(t.costo_aplicado || 0), 0))
watch(totalTareas, val => emit('total-changed', val), { immediate: true })

async function load() {
  try {
    loading.value = true
    if (tipos.value.length === 0) {
      tipos.value = await fetchTiposTrabajo()
    }
    tareas.value = await fetchTareas(props.clienteId, props.ordenId, props.equipoId)
  } catch (e: any) {
    toast.error(e?.response?.data?.message || 'No se pudieron cargar las tareas.')
  } finally {
    loading.value = false
  }
}

watch(() => props.equipoId, (id) => { if (id) load() }, { immediate: true })

async function agregar() {
  if (!newTask.value.tipo_trabajo_id) {
    toast.warning('Selecciona un tipo de trabajo'); return
  }
  if (!newTask.value.costo_aplicado) {
    toast.warning('Ingresa el costo de la tarea'); return
  }
  
  try {
    adding.value = true
    await createTarea(props.clienteId, props.ordenId, props.equipoId, {
      tipo_trabajo_id: newTask.value.tipo_trabajo_id,
      costo_aplicado: newTask.value.costo_aplicado,
    })
    await load()
    newTask.value = { tipo_trabajo_id: 0, costo_aplicado: null }
    toast.success('¡Tarea agregada exitosamente! 🎉')
    emit('changed')
    flashAddBlock()
  } catch (e: any) {
    toast.error(e?.response?.data?.message || 'No se pudo agregar la tarea.')
  } finally {
    adding.value = false
  }
}

async function eliminar(t: Tarea) {
  if (!confirm('¿Estás seguro de que deseas eliminar esta tarea?')) return
  
  try {
    await deleteTarea(props.clienteId, props.ordenId, props.equipoId, t.id)
    await load()
    emit('changed')
    toast.success('¡Tarea eliminada exitosamente! 🗑️')
  } catch (e: any) {
    toast.error(e?.response?.data?.message || 'No se pudo eliminar la tarea.')
  }
}

function flashAddBlock() {
  addHighlight.value = true
  setTimeout(() => (addHighlight.value = false), 1000)
}

function estadoLabel(e: string) {
  const map: Record<string,string> = {
    pendiente: 'Pendiente',
    en_proceso: 'En Proceso',
    completada: 'Completada',
    cancelada: 'Cancelada'
  }
  return map[e] || e
}

function estadoClass(e: string) {
  const map: Record<string,string> = {
    pendiente: 'bg-yellow-100 text-yellow-800 border border-yellow-300',
    en_proceso: 'bg-blue-100 text-blue-800 border border-blue-300',
    completada: 'bg-green-100 text-green-800 border border-green-300',
    cancelada: 'bg-gray-100 text-gray-800 border border-gray-300'
  }
  return map[e] || 'bg-gray-100 text-gray-800 border border-gray-300'
}

function estadoIconClass(e: string) {
  const map: Record<string,string> = {
    pendiente: 'bg-yellow-100 text-yellow-600',
    en_proceso: 'bg-blue-100 text-blue-600',
    completada: 'bg-green-100 text-green-600',
    cancelada: 'bg-gray-100 text-gray-600'
  }
  return map[e] || 'bg-gray-100 text-gray-600'
}

function estadoDotClass(e: string) {
  const map: Record<string,string> = {
    pendiente: 'bg-yellow-500',
    en_proceso: 'bg-blue-500',
    completada: 'bg-green-500',
    cancelada: 'bg-gray-500'
  }
  return map[e] || 'bg-gray-500'
}

function estadoIconPath(e: string) {
  const map: Record<string,string> = {
    pendiente: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
    en_proceso: 'M13 10V3L4 14h7v7l9-11h-7z',
    completada: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    cancelada: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'
  }
  return map[e] || 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
}

function nombreTrabajo(id: number) {
  return tipos.value.find(x => x.id === id)?.nombre || `Trabajo #${id}`
}

const money = (n: number) =>
  n.toLocaleString('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 })
</script>