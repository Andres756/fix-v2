<template>
  <div class="mt-3">
    <h6 class="font-semibold">Historial</h6>
    <ul v-if="historial.length" class="text-sm text-gray-600 space-y-1">
      <li v-for="h in historial" :key="h.id">
        {{ h.cambiado_en }}: {{ h.estado_anterior }} → {{ h.estado_nuevo }} (por {{ h.tecnico?.nombre || 'Sistema' }})
      </li>
    </ul>
    <p v-else class="text-gray-500 text-sm">Sin historial</p>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { fetchHistorialTarea } from '../api/tecnicos'
import type { HistorialTarea } from '../types/tecnico'

const props = defineProps<{ tecnicoId: number; tareaId: number }>()

const historial = ref<HistorialTarea[]>([])

async function loadHistorial() {
  historial.value = await fetchHistorialTarea(props.tecnicoId, props.tareaId)
}

onMounted(loadHistorial)
watch(() => props.tareaId, loadHistorial)
</script>
