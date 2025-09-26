<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Panel de Técnicos</h1>
      <p class="text-gray-600 mt-2">Gestiona equipos, tareas y seguimiento de trabajos</p>
    </div>

    <!-- Selector de Técnico (Solo Admin) -->
    <div v-if="user?.role === 'admin'" class="bg-white rounded-lg shadow-sm border p-6 mb-6">
      <label class="block text-sm font-medium text-gray-700 mb-2">
        Seleccionar Técnico:
      </label>
      <div class="custom-select max-w-md">
        <select v-model="selectedTecnicoId">
          <option value="">-- Selecciona un técnico --</option>
          <option v-for="t in tecnicos" :key="t.id" :value="t.id">
            {{ t.nombre }}
          </option>
        </select>
      </div>
    </div>

    <!-- Dashboard Content -->
    <div v-if="selectedTecnicoId && !loading">
      
      <!-- Métricas Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <MetricCard
          title="Total Equipos"
          :value="dashboardData?.stats?.total_equipos || 0"
          icon="📱"
          color="blue"
        />
        <MetricCard
          title="En Proceso"
          :value="dashboardData?.stats?.equipos_en_proceso || 0"
          icon="⚙️"
          color="yellow"
        />
        <MetricCard
          title="Completados"
          :value="dashboardData?.stats?.equipos_completados || 0"
          icon="✅"
          color="green"
        />
        <MetricCard
          title="Total Tareas"
          :value="dashboardData?.stats?.total_tareas || 0"
          icon="📋"
          color="purple"
        />
      </div>

      <!-- Progreso de Tareas -->
      <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Progreso de Tareas</h3>
        <TareasProgress :stats="dashboardData?.stats" />
      </div>

      <!-- Lista de Equipos -->
      <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">Equipos Asignados</h3>
        </div>
        
        <div class="divide-y divide-gray-200">
          <EquipoCard
            v-for="equipo in dashboardData?.equipos || []"
            :key="equipo.id"
            :equipo="equipo"
            :tecnico-id="selectedTecnicoId"
            @tarea-updated="reloadDashboard"
          />
        </div>
        
        <div v-if="!dashboardData?.equipos?.length" class="p-8 text-center">
          <div class="text-gray-400 text-lg mb-2">📭</div>
          <p class="text-gray-500">No hay equipos asignados</p>
        </div>
      </div>

    </div>

    <!-- Loading State -->
    <div v-else-if="loading" class="flex items-center justify-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <span class="ml-3 text-gray-600">Cargando dashboard...</span>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12">
      <div class="text-gray-400 text-6xl mb-4">👨‍🔧</div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">Selecciona un técnico</h3>
      <p class="text-gray-500">Elige un técnico para ver su dashboard</p>
    </div>

  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { getUser } from '../../auth/auth'
import MetricCard from '../../features/Tecnico/component/MetricCard.vue'
import TareasProgress from '../../features/Tecnico/component/TareasProgress.vue'
import EquipoCard from '../../features/Tecnico/component/EquipoCard.vue'
import { fetchTecnicos, fetchDashboard } from '../../features/Tecnico/api/tecnicos'
import type { Tecnico, DashboardData } from '../../features/Tecnico/types/tecnico'

const user = getUser()

// Estado
const tecnicos = ref<Tecnico[]>([])
const selectedTecnicoId = ref<number | null>(null)
const dashboardData = ref<DashboardData | null>(null)
const loading = ref(false)

// Inicializar
onMounted(async () => {
  if (user?.role === 'admin') {
    try {
      tecnicos.value = await fetchTecnicos()
    } catch (error) {
      console.error('Error cargando técnicos:', error)
    }
  } else if (user?.role === 'tecnico') {
    selectedTecnicoId.value = user.id
  }
})

// Cargar dashboard cuando cambia el técnico
watch(selectedTecnicoId, async (newTecnicoId) => {
  if (newTecnicoId) {
    await loadDashboard()
  } else {
    dashboardData.value = null
  }
})

// Cargar datos del dashboard
async function loadDashboard() {
  if (!selectedTecnicoId.value) return
  
  loading.value = true
  try {
    dashboardData.value = await fetchDashboard(selectedTecnicoId.value)
  } catch (error) {
    console.error('Error cargando dashboard:', error)
  } finally {
    loading.value = false
  }
}

// Recargar dashboard
async function reloadDashboard() {
  await loadDashboard()
}
</script>