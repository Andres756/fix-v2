<template>
  <div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h4 class="text-sm font-medium text-gray-700">
          📦 Repuestos del Inventario
        </h4>
        <p class="text-xs text-gray-500 mt-1">
          Se descontará automáticamente del stock
        </p>
      </div>
    </div>

    <!-- Formulario agregar repuesto -->
    <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
        <!-- Buscador -->
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">
            Buscar repuesto
          </label>
          <input
            v-model="search"
            @input="buscarInventario"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            placeholder="Código o nombre..."
          />
          <!-- Resultados búsqueda -->
          <ul
            v-if="resultados.length"
            class="absolute z-10 mt-1 w-full max-w-md bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto"
          >
            <li
              v-for="item in resultados"
              :key="item.id"
              @click="selectInventario(item)"
              class="px-3 py-2 hover:bg-gray-100 cursor-pointer text-sm border-b last:border-b-0"
            >
              <div class="font-medium text-gray-900">{{ item.codigo }}</div>
              <div class="text-xs text-gray-500">
                {{ item.nombre }} - Stock: {{ item.stock }}
              </div>
            </li>
          </ul>
        </div>

        <!-- Cantidad -->
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">
            Cantidad
          </label>
          <input
            type="number"
            min="1"
            v-model.number="newItem.cantidad"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
          />
        </div>

        <!-- Botón agregar -->
        <div>
          <button
            @click="agregar"
            :disabled="adding || !newItem.inventario_id"
            class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ adding ? 'Agregando…' : '+ Agregar' }}
          </button>
        </div>
      </div>

      <!-- Observaciones -->
      <div class="mt-3">
        <label class="block text-xs font-medium text-gray-600 mb-1">
          Observaciones (opcional)
        </label>
        <input
          v-model="newItem.observaciones"
          type="text"
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
          placeholder="Ej: Display usado en reparación..."
        />
      </div>
    </div>

    <!-- Tabla de repuestos agregados -->
    <div class="overflow-x-auto border border-gray-200 rounded-lg">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">
              Código
            </th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">
              Nombre
            </th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">
              Cantidad
            </th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">
              Costo Unit.
            </th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">
              Costo Total
            </th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">
              Acciones
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="r in repuestos"
            :key="r.id"
            class="border-t border-gray-200 hover:bg-gray-50"
          >
            <td class="px-4 py-3 text-sm text-gray-900">
              {{ r.inventario_codigo }}
            </td>
            <td class="px-4 py-3 text-sm text-gray-900">
              {{ r.inventario_nombre }}
            </td>
            <td class="px-4 py-3 text-sm text-gray-900">
              {{ r.cantidad }}
            </td>
            <td class="px-4 py-3 text-sm text-gray-900">
              {{ formatCurrency(r.costo_unitario) }}
            </td>
            <td class="px-4 py-3 text-sm font-medium text-gray-900">
              {{ formatCurrency(r.costo_total) }}
            </td>
            <td class="px-4 py-3 text-right">
              <button
                @click="eliminar(r.id)"
                class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200 transition-colors"
              >
                Eliminar
              </button>
            </td>
          </tr>
          <tr v-if="!loading && repuestos.length === 0">
            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">
              No hay repuestos de inventario agregados
            </td>
          </tr>
          <tr v-if="loading">
            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">
              Cargando repuestos…
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { toast } from 'vue3-toastify'
import http from '../../../shared/api/http'
import {
  fetchCostosAdicionales,
  addRepuestoInventario,
  deleteCostoAdicional,
} from '../api/costosAdicionales'
import type { RepuestoInventario, CreateRepuestoInventarioPayload } from '../api/costosAdicionales'

const props = defineProps<{
  entradaId: number
}>()

const emit = defineEmits<{
  changed: []
}>()

// Estado
const repuestos = ref<RepuestoInventario[]>([])
const resultados = ref<any[]>([])
const search = ref('')
const loading = ref(false)
const adding = ref(false)

const newItem = ref<CreateRepuestoInventarioPayload & { inventario_id: number | null }>({
  inventario_id: null,
  cantidad: 1,
  observaciones: '',
})

// Métodos
async function load() {
  try {
    loading.value = true
    const costos = await fetchCostosAdicionales(props.entradaId)
    repuestos.value = costos.repuestos_inventario
  } catch (e: any) {
    console.error('Error cargando repuestos:', e)
    toast.error('No se pudieron cargar los repuestos')
  } finally {
    loading.value = false
  }
}

async function buscarInventario() {
  if (search.value.length < 2) {
    resultados.value = []
    return
  }
  try {
    const { data } = await http.get('/inventario/repuestos/search', {
      params: { q: search.value },
    })
    resultados.value = data.data || data || []
  } catch (e) {
    console.error('Error buscando inventario:', e)
    resultados.value = []
  }
}

function selectInventario(item: any) {
  newItem.value.inventario_id = item.id
  resultados.value = []
  search.value = `${item.codigo} — ${item.nombre}`
}

async function agregar() {
  if (!newItem.value.inventario_id) {
    toast.warning('Selecciona un repuesto')
    return
  }

  try {
    adding.value = true
    await addRepuestoInventario(props.entradaId, {
      inventario_id: newItem.value.inventario_id,
      cantidad: newItem.value.cantidad,
      observaciones: newItem.value.observaciones || undefined,
    })
    
    await load()
    
    // Resetear formulario
    newItem.value = {
      inventario_id: null,
      cantidad: 1,
      observaciones: '',
    }
    search.value = ''
    
    toast.success('Repuesto agregado correctamente')
    emit('changed')
  } catch (e: any) {
    console.error('Error agregando repuesto:', e)
    toast.error(e?.response?.data?.message || 'No se pudo agregar el repuesto')
  } finally {
    adding.value = false
  }
}

async function eliminar(id: number) {
  if (!confirm('¿Eliminar este repuesto?')) return

  try {
    await deleteCostoAdicional(props.entradaId, 'repuesto-inventario', id)
    await load()
    toast.success('Repuesto eliminado')
    emit('changed')
  } catch (e: any) {
    console.error('Error eliminando repuesto:', e)
    toast.error('No se pudo eliminar el repuesto')
  }
}

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
  }).format(value)
}

// Lifecycle
watch(
  () => props.entradaId,
  (id) => {
    if (id) load()
  },
  { immediate: true }
)
</script>