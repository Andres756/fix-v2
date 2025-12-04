<template>
  <div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h4 class="text-sm font-medium text-gray-700">
          🛒 Repuestos Externos
        </h4>
        <p class="text-xs text-gray-500 mt-1">
          Repuestos comprados fuera del inventario
        </p>
      </div>
    </div>

    <!-- Formulario agregar repuesto externo -->
    <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <!-- Descripción -->
        <div class="md:col-span-2">
          <label class="block text-xs font-medium text-gray-600 mb-1">
            Descripción *
          </label>
          <input
            v-model="newItem.descripcion"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            placeholder="Ej: Batería compatible iPhone 14"
          />
        </div>

        <!-- Cantidad -->
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">
            Cantidad *
          </label>
          <input
            type="number"
            min="1"
            v-model.number="newItem.cantidad"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
          />
        </div>

        <!-- Costo Unitario -->
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">
            Costo Unit. *
          </label>
          <input
            type="number"
            min="0"
            step="0.01"
            v-model.number="newItem.costo_unitario"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            placeholder="0.00"
          />
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
        <!-- Proveedor (opcional) -->
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">
            Proveedor (opcional)
          </label>
          <select
            v-model="newItem.proveedor_id"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
          >
            <option :value="null">Sin proveedor</option>
            <option v-for="prov in proveedores" :key="prov.id" :value="prov.id">
              {{ prov.nombre }}
            </option>
          </select>
        </div>

        <!-- Observaciones -->
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">
            Observaciones (opcional)
          </label>
          <input
            v-model="newItem.observaciones"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            placeholder="Notas adicionales..."
          />
        </div>
      </div>

      <!-- Botón agregar -->
      <div class="mt-3">
        <button
          @click="agregar"
          :disabled="adding || !puedeAgregar"
          class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ adding ? 'Agregando…' : '+ Agregar Repuesto Externo' }}
        </button>
      </div>
    </div>

    <!-- Tabla de repuestos externos -->
    <div class="overflow-x-auto border border-gray-200 rounded-lg">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">
              Descripción
            </th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">
              Proveedor
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
              {{ r.descripcion }}
            </td>
            <td class="px-4 py-3 text-sm text-gray-600">
              {{ r.proveedor_nombre || 'N/A' }}
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
              No hay repuestos externos agregados
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
import { ref, computed, watch, onMounted } from 'vue'
import { toast } from 'vue3-toastify'
import http from '../../../shared/api/http'
import {
  fetchCostosAdicionales,
  addRepuestoExterno,
  deleteCostoAdicional,
} from '../api/costosAdicionales'
import type { RepuestoExterno, CreateRepuestoExternoPayload } from '../api/costosAdicionales'

const props = defineProps<{
  entradaId: number
}>()

const emit = defineEmits<{
  changed: []
}>()

// Estado
const repuestos = ref<RepuestoExterno[]>([])
const proveedores = ref<any[]>([])
const loading = ref(false)
const adding = ref(false)

const newItem = ref<CreateRepuestoExternoPayload>({
  descripcion: '',
  cantidad: 1,
  costo_unitario: 0,
  proveedor_id: undefined,
  observaciones: '',
})

// Computed
const puedeAgregar = computed(() => {
  return (
    newItem.value.descripcion.trim() !== '' &&
    newItem.value.cantidad > 0 &&
    newItem.value.costo_unitario >= 0
  )
})

// Métodos
async function load() {
  try {
    loading.value = true
    const costos = await fetchCostosAdicionales(props.entradaId)
    repuestos.value = costos.repuestos_externos
  } catch (e: any) {
    console.error('Error cargando repuestos externos:', e)
    toast.error('No se pudieron cargar los repuestos externos')
  } finally {
    loading.value = false
  }
}

async function cargarProveedores() {
  try {
    const { data } = await http.get('/inventario/proveedores', {
      params: { per_page: 1000 },
    })
    proveedores.value = data.data || data || []
  } catch (e) {
    console.error('Error cargando proveedores:', e)
  }
}

async function agregar() {
  if (!puedeAgregar.value) {
    toast.warning('Completa todos los campos requeridos')
    return
  }

  try {
    adding.value = true
    await addRepuestoExterno(props.entradaId, {
      descripcion: newItem.value.descripcion,
      cantidad: newItem.value.cantidad,
      costo_unitario: newItem.value.costo_unitario,
      proveedor_id: newItem.value.proveedor_id || undefined,
      observaciones: newItem.value.observaciones || undefined,
    })

    await load()

    // Resetear formulario
    newItem.value = {
      descripcion: '',
      cantidad: 1,
      costo_unitario: 0,
      proveedor_id: undefined,
      observaciones: '',
    }

    toast.success('Repuesto externo agregado correctamente')
    emit('changed')
  } catch (e: any) {
    console.error('Error agregando repuesto externo:', e)
    toast.error(e?.response?.data?.message || 'No se pudo agregar el repuesto')
  } finally {
    adding.value = false
  }
}

async function eliminar(id: number) {
  if (!confirm('¿Eliminar este repuesto externo?')) return

  try {
    await deleteCostoAdicional(props.entradaId, 'repuesto-externo', id)
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
onMounted(() => {
  cargarProveedores()
})

watch(
  () => props.entradaId,
  (id) => {
    if (id) load()
  },
  { immediate: true }
)
</script>