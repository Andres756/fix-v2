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
            step="1"
            v-model.number="newItem.costo_unitario"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            placeholder="0"
          />
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
        <!-- Proveedor (opcional) -->
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">
            Proveedor (opcional)
          </label>
          <select
            v-model="newItem.proveedor_id"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
          >
            <option :value="undefined">Sin proveedor</option>
            <option v-for="prov in proveedores" :key="prov.id" :value="prov.id">
              {{ prov.nombre }}
            </option>
          </select>
        </div>

        <!-- NUEVO: Checkbox A Crédito -->
        <div class="flex items-end">
          <label class="flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
            <input
              type="checkbox"
              v-model="newItem.a_credito"
              class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
            />
            <span class="text-sm font-medium text-gray-700">A Crédito</span>
          </label>
        </div>

        <!-- NUEVO: Método de Pago (solo si NO es a crédito) -->
        <div v-if="!newItem.a_credito">
          <label class="block text-xs font-medium text-gray-600 mb-1">
            Método de Pago *
          </label>
          <select
            v-model="newItem.metodo_pago_id"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
          >
            <option :value="undefined">Seleccionar método</option>
            <option v-for="metodo in metodosPago" :key="metodo.id" :value="metodo.id">
              {{ metodo.nombre }}
            </option>
          </select>
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
          placeholder="Notas adicionales..."
        />
      </div>

      <!-- Mensaje informativo -->
      <div v-if="!newItem.a_credito && newItem.metodo_pago_id" class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded-lg">
        <p class="text-xs text-blue-700 flex items-center gap-1">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          Se descontará automáticamente del método de pago seleccionado
        </p>
      </div>

      <div v-if="newItem.a_credito" class="mt-2 p-2 bg-amber-50 border border-amber-200 rounded-lg">
        <p class="text-xs text-amber-700 flex items-center gap-1">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          Se registrará como gasto pendiente en el módulo de Gastos
        </p>
      </div>

      <!-- Botón agregar -->
      <div class="mt-3">
        <button
          @click="agregar"
          :disabled="adding || !puedeAgregar"
          class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
        >
          <svg v-if="!adding" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
          <svg v-else class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          {{ adding ? 'Agregando…' : 'Agregar Repuesto Externo' }}
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
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">
              Estado
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
              <span v-if="r.observaciones" class="block text-xs text-gray-500 mt-1">
                {{ r.observaciones }}
              </span>
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
            <td class="px-4 py-3 text-sm">
              <span 
                v-if="r.a_credito"
                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800"
              >
                A Crédito
              </span>
              <span 
                v-else
                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
              >
                {{ r.metodo_pago_nombre || 'Pagado' }}
              </span>
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
            <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">
              No hay repuestos externos agregados
            </td>
          </tr>
          <tr v-if="loading">
            <td colspan="7" class="px-4 py-6">
              <div class="animate-pulse space-y-3">
                <div class="flex gap-4">
                  <div class="h-4 bg-gray-200 rounded w-2/5"></div>
                  <div class="h-4 bg-gray-200 rounded w-1/5"></div>
                  <div class="h-4 bg-gray-200 rounded w-1/6"></div>
                  <div class="h-4 bg-gray-200 rounded w-1/5"></div>
                  <div class="h-4 bg-gray-200 rounded flex-1"></div>
                </div>
                <div class="flex gap-4">
                  <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                  <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                  <div class="h-4 bg-gray-200 rounded w-1/6"></div>
                  <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                  <div class="h-4 bg-gray-200 rounded flex-1"></div>
                </div>
              </div>
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
} from '../api/CostosAdicionales'
import type { RepuestoExterno, CreateRepuestoExternoPayload } from '../api/CostosAdicionales'

const props = defineProps<{
  entradaId: number
}>()

const emit = defineEmits<{
  changed: []
}>()

// Estado
const repuestos = ref<RepuestoExterno[]>([])
const proveedores = ref<any[]>([])
const metodosPago = ref<any[]>([])
const loading = ref(false)
const adding = ref(false)

const newItem = ref<CreateRepuestoExternoPayload>({
  descripcion: '',
  cantidad: 1,
  costo_unitario: 0,
  proveedor_id: undefined,
  observaciones: '',
  a_credito: false,
  metodo_pago_id: undefined,
})

// Computed
const puedeAgregar = computed(() => {
  return (
    newItem.value.descripcion.trim() !== '' &&
    newItem.value.cantidad > 0 &&
    newItem.value.costo_unitario >= 0 &&
    // Si NO es a crédito, debe tener método de pago
    (newItem.value.a_credito || newItem.value.metodo_pago_id !== undefined)
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

async function cargarMetodosPago() {
  try {
    const { data } = await http.get('/parametros/formas-pago/options')
    metodosPago.value = data.data || data || []
  } catch (e) {
    console.error('Error cargando métodos de pago:', e)
  }
}

async function agregar() {
  if (!puedeAgregar.value) {
    toast.warning('Completa todos los campos requeridos')
    return
  }

  // Validación adicional
  if (!newItem.value.a_credito && !newItem.value.metodo_pago_id) {
    toast.warning('Debes seleccionar un método de pago')
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
      a_credito: newItem.value.a_credito,
      metodo_pago_id: newItem.value.metodo_pago_id || undefined,
    })

    await load()

    // Resetear formulario
    newItem.value = {
      descripcion: '',
      cantidad: 1,
      costo_unitario: 0,
      proveedor_id: undefined,
      observaciones: '',
      a_credito: false,
      metodo_pago_id: undefined,
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
  cargarMetodosPago()
})

watch(
  () => props.entradaId,
  (id) => {
    if (id) load()
  },
  { immediate: true }
)
</script>