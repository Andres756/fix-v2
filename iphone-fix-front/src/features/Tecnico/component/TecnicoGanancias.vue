<template>
  <div class="bg-gradient-to-br from-emerald-50 to-green-50 border border-green-200 rounded-lg p-4">
    <h3 class="font-semibold text-green-800 mb-2">Resumen de Ganancias</h3>
    <p class="text-lg font-bold text-green-700 mb-3">
      Total ganado: {{ money(resumen?.total_ganado || 0) }}
    </p>
    <ul class="space-y-1">
      <li v-for="eq in resumen?.equipos" :key="eq.equipo_os_id">
        {{ eq.modelo }} → {{ money(eq.comision.ganancia) }}
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { fetchGanancias } from '../api/tecnicos'
import type { GananciasTecnico } from '../types/tecnico'

const props = defineProps<{ tecnicoId: number }>()

const resumen = ref<GananciasTecnico | null>(null)

async function loadGanancias() {
  if (!props.tecnicoId) return
  resumen.value = await fetchGanancias(props.tecnicoId)
}

// 🔥 cada vez que cambie el técnico, vuelves a cargar
watch(() => props.tecnicoId, () => {
  loadGanancias()
}, { immediate: true })

const money = (n: number) =>
  new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(n)
</script>
