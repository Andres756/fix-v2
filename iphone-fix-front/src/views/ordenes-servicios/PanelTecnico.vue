<template>
  <AppLayout>
    <h1 class="text-2xl font-bold mb-6">Panel de Técnicos</h1>

    <!-- ✅ Solo admins pueden elegir técnico -->
    <div v-if="user?.role === 'admin'" class="mb-6">
      <label for="tecnico" class="block text-sm font-medium text-gray-700">
        Seleccionar Técnico:
      </label>
      <select
        id="tecnico"
        v-model="selectedTecnicoId"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
      >
        <option disabled value="">-- Selecciona un técnico --</option>
        <option v-for="t in tecnicos" :key="t.id" :value="t.id">
          {{ t.nombre }}
        </option>
      </select>
    </div>

    <!-- ✅ Equipos asignados -->
    <TecnicoEquipos
      v-if="selectedTecnicoId"
      :equipos="equipos"
      :tecnico-id="selectedTecnicoId"
      @estado-actualizado="loadDatosTecnico"
    />

    <!-- Resumen de ganancias -->
    <TecnicoGanancias
      v-if="selectedTecnicoId"
      :tecnico-id="selectedTecnicoId"
    />
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import TecnicoEquipos from '../../features/Tecnico/component/TecnicoEquipos.vue'
import TecnicoGanancias from '../../features/Tecnico/component/TecnicoGanancias.vue'

import { getUser } from '../../auth/auth'
import {
  fetchTecnicos,
  fetchEquiposAsignados,
  fetchGanancias
} from '../../features/Tecnico/api/tecnicos'
import type { Tecnico, EquipoAsignado, GananciasTecnico } from '../../features/Tecnico/types/tecnico'

const user = getUser()

// Estado
const tecnicos = ref<Tecnico[]>([])
const selectedTecnicoId = ref<number | null>(null)
const equipos = ref<EquipoAsignado[]>([])
const ganancias = ref<GananciasTecnico | null>(null)

// Cargar lista de técnicos si es admin
onMounted(async () => {
  if (user?.role === 'admin') {
    try {
      tecnicos.value = await fetchTecnicos()
    } catch (e) {
      console.error('Error cargando técnicos:', e)
    }
  } else if (user?.role === 'tecnico') {
    selectedTecnicoId.value = user.id
  }
})

// Reaccionar al cambio de técnico
watch(selectedTecnicoId, async (nuevoId) => {
  if (nuevoId) {
    await loadDatosTecnico()
  }
})

async function loadDatosTecnico() {
  if (!selectedTecnicoId.value) return
  try {
    equipos.value = await fetchEquiposAsignados(selectedTecnicoId.value)
    ganancias.value = await fetchGanancias(selectedTecnicoId.value)
  } catch (e) {
    console.error('Error cargando datos del técnico:', e)
  }
}
</script>
