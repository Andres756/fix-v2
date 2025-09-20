<template>
  <div>
    <h3 class="text-xl font-semibold mb-2">Equipos Asignados</h3>
    <div v-if="loading">Cargando equipos...</div>
    <div v-else-if="error" class="text-red-600">{{ error }}</div>
    <div v-else>
      <div
        v-for="equipo in equipos"
        :key="equipo.id"
        class="border rounded-lg p-4 mb-3 bg-white shadow"
      >
        <h4 class="font-bold">{{ equipo.marca }} {{ equipo.modelo }}</h4>
        <p class="text-gray-600 text-sm">IMEI: {{ equipo.imei_serial }}</p>
        <p class="text-sm mb-2">
          Estado: <span class="font-semibold">{{ equipo.estado }}</span>
        </p>

        <!-- Tareas -->
        <TecnicoTareas :tecnicoId="tecnicoId" :tareas="equipo.tareas" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { fetchEquiposAsignados } from '../api/tecnicos'
import type { EquipoAsignado } from '../types/tecnico'
import TecnicoTareas from './TecnicoTareas.vue'

const props = defineProps<{ tecnicoId: number }>()

const equipos = ref<EquipoAsignado[]>([])
const loading = ref(false)
const error = ref<string | null>(null)

async function loadEquipos() {
  if (!props.tecnicoId) return
  try {
    loading.value = true
    equipos.value = await fetchEquiposAsignados(props.tecnicoId)
    error.value = null
  } catch (e) {
    error.value = 'No se pudieron cargar los equipos'
  } finally {
    loading.value = false
  }
}

// 🔥 Cargar al inicio y cuando cambie el técnico
watch(
  () => props.tecnicoId,
  () => loadEquipos(),
  { immediate: true }
)
</script>
