<template>
  <div>
    <h5 class="font-semibold mb-2">Tareas</h5>
    <ul class="space-y-2">
      <li v-for="t in tareas" :key="t.id" class="flex justify-between items-center border p-2 rounded">
        <div>
          <p class="font-medium">{{ t.nombre }}</p>
          <p class="text-sm text-gray-500">Estado: {{ t.estado }}</p>
          <p class="text-sm text-emerald-700">Costo: {{ money(t.costo_aplicado) }}</p>
        </div>
        <div class="flex space-x-2">
          <button v-for="estado in estados" :key="estado"
            @click="cambiarEstado(t.id, estado)"
            class="px-2 py-1 text-sm rounded border"
            :class="{
              'bg-blue-100 text-blue-700': estado === 'en_proceso',
              'bg-green-100 text-green-700': estado === 'completada',
              'bg-gray-100 text-gray-700': estado === 'pendiente',
              'bg-red-100 text-red-700': estado === 'cancelada'
            }">
            {{ estado }}
          </button>
        </div>
      </li>
    </ul>

    <!-- Historial -->
    <TecnicoHistorial v-if="selectedTareaId" :tecnicoId="tecnicoId" :tareaId="selectedTareaId" />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { updateTareaEstado } from '../api/tecnicos'
import TecnicoHistorial from './TecnicoHistorial.vue'

const props = defineProps<{ tecnicoId: number; tareas: any[] }>()

const estados = ['pendiente', 'en_proceso', 'completada', 'cancelada']
const selectedTareaId = ref<number|null>(null)

async function cambiarEstado(tareaId: number, estado: string) {
  await updateTareaEstado(props.tecnicoId, tareaId, estado)
  selectedTareaId.value = tareaId
}

const money = (n: number) =>
  new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(n)
</script>
