<template>
  <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-5 border border-green-200 sticky top-4">
    <!-- Header -->
    <div class="flex items-center space-x-3 mb-4">
      <div class="p-2 bg-green-100 rounded-lg">
        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
      </div>
      <h5 class="font-semibold text-green-800">Resumen Financiero</h5>
    </div>

    <!-- Body -->
    <div v-if="loading" class="text-gray-600 text-sm">Cargando costos...</div>
    <div v-else-if="error" class="text-red-600 text-sm">{{ error }}</div>
    <div v-else-if="resumen" class="space-y-3">
      <div class="flex justify-between items-center py-2">
        <span class="text-sm text-gray-600">Valor estimado:</span>
        <span class="font-semibold text-blue-700">{{ formatCurrency(resumen.valor_estimado) }}</span>
      </div>

      <div class="flex justify-between items-center py-2">
        <span class="text-sm text-gray-600">Costo actividades:</span>
        <span class="font-semibold text-green-700">{{ formatCurrency(resumen.costo_actividades) }}</span>
      </div>

      <div class="flex justify-between items-center py-2">
        <span class="text-sm text-gray-600">Costo repuestos:</span>
        <span class="font-semibold text-green-700">{{ formatCurrency(resumen.costo_repuestos) }}</span>
      </div>

      <div class="flex justify-between items-center py-2">
        <span class="text-sm text-gray-600">Costo externos:</span>
        <span class="font-semibold text-green-700">{{ formatCurrency(resumen.costo_externos) }}</span>
      </div>

      <div class="flex justify-between items-center py-2 border-t border-green-200 pt-2">
        <span class="text-sm text-gray-600">Costo real:</span>
        <span class="text-lg font-bold text-green-800">{{ formatCurrency(resumen.costo_real) }}</span>
      </div>

      <div class="flex justify-between items-center py-2">
        <span class="text-sm text-gray-600">Diferencia:</span>
        <span
          :class="{
            'text-red-600 font-bold': resumen.diferencia > 0,
            'text-green-600 font-bold': resumen.diferencia < 0,
            'text-gray-600 font-semibold': resumen.diferencia === 0,
          }"
        >
          {{ formatCurrency(resumen.diferencia) }}
        </span>
      </div>

      <div class="flex justify-between items-center py-2">
        <span class="text-sm text-gray-600">Estado presupuesto:</span>
        <span
          :class="{
            'text-red-600 font-bold': resumen.estado_presupuesto === 'superado',
            'text-green-600 font-bold': resumen.estado_presupuesto === 'por_debajo',
            'text-gray-600 font-semibold': resumen.estado_presupuesto === 'exacto',
          }"
        >
          {{ resumen.estado_presupuesto }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue"
import type { EquipoCostos } from "../types/equipo"
import { getEquipoCostos } from "../api/equipos"

interface Props {
  equipoId: number
}
const props = defineProps<Props>()

const resumen = ref<EquipoCostos | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)

const fetchResumen = async () => {
  loading.value = true
  try {
    resumen.value = await getEquipoCostos(props.equipoId)
  } catch (err) {
    error.value = "No se pudo cargar el resumen de costos"
  } finally {
    loading.value = false
  }
}
onMounted(fetchResumen)

const formatCurrency = (value: number) =>
  new Intl.NumberFormat("es-CO", {
    style: "currency",
    currency: "COP",
    minimumFractionDigits: 0,
  }).format(value)
</script>
