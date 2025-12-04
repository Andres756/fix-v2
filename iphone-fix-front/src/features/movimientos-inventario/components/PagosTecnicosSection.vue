<template>
  <div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h4 class="text-sm font-medium text-gray-700">
          👨‍🔧 Pagos a Técnicos
        </h4>
        <p class="text-xs text-gray-500 mt-1">
          Mano de obra y servicios técnicos
        </p>
      </div>
    </div>

    <!-- Formulario agregar pago -->
    <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <!-- Descripción -->
        <div class="md:col-span-2">
          <label class="block text-xs font-medium text-gray-600 mb-1">
            Descripción del trabajo *
          </label>
          <input
            v-model="newItem.descripcion"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            placeholder="Ej: Cambio de display y batería"
          />
        </div>

        <!-- Técnico -->
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">
            Técnico (opcional)
          </label>
          <select
            v-model="newItem.tecnico_id"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
          >
            <option :value="null">Sin asignar</option>
            <option v-for="tec in tecnicos" :key="tec.id" :value="tec.id">
              {{ tec.name }}
            </option>
          </select>
        </div>

        <!-- Tipo de pago -->
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">
            Tipo de pago *
          </label>
          <select
            v-model="newItem.tipo_pago"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
          >
            <option value="fijo">💵 Monto Fijo</option>
            <option value="porcentaje">📊 Porcentaje</option>
          </select>
        </div>

        <!-- Valor -->
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">
            {{ newItem.tipo_pago === 'porcentaje' ? 'Porcentaje (%)' : 'Valor ($)' }} *
          </label>
          <input
            type="number"
            min="0"
            step="0.01"
            v-model.number="newItem.valor"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            :placeholder="newItem.tipo_pago === 'porcentaje' ? '10.00' : '50000'"
          />
          <p v-if="newItem.tipo_pago === 'porcentaje'" class="text-xs text-gray-500 mt-1">
            Se calculará sobre el costo base de la entrada
          </p>
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
          {{ adding ? 'Agregando…' : '+ Agregar Pago' }}
        </button>
      </div>
    </div>

    <!-- Tabla de pagos -->
    <div class="overflow-x-auto border border-gray-200 rounded-lg">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">
              Descripción
            </th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">
              Técnico
            </th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">
              Tipo
            </th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">
              Valor
            </th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">
              Costo Calculado
            </th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">
              Acciones
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="p in pagos"
            :key="p.id"
            class="border-t border-gray-200 hover:bg-gray-50"
          >
            <td class="px-4 py-3 text-sm text-gray-900">
              {{ p.descripcion }}
            </td>
            <td class="px-4 py-3 text-sm text-gray-600">
              {{ p.tecnico_nombre || 'Sin asignar' }}
            </td>
            <td class="px-4 py-3 text-sm">
              <span
                :class="
                  p.tipo_pago === 'fijo'
                    ? 'bg-blue-100 text-blue-700'
                    : 'bg-green-100 text-green-700'
                "
                class="px-2 py-1 rounded text-xs font-medium"
              >
                {{ p.tipo_pago === 'fijo' ? '💵 Fijo' : '📊 Porcentaje' }}
              </span>
            </td>
            <td class="px-4 py-3 text-sm text-gray-900">
              {{ p.tipo_pago === 'porcentaje' ? `${p.valor}%` : formatCurrency(p.valor) }}
            </td>
            <td class="px-4 py-3 text-sm font-medium text-gray-900">
              {{ formatCurrency(p.costo_calculado) }}
            </td>
            <td class="px-4 py-3 text-right">
              <button
                @click="eliminar(p.id)"
                class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200 transition-colors"
              >
                Eliminar
              </button>
            </td>
          </tr>
          <tr v-if="!loading && pagos.length === 0">
            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">
              No hay pagos a técnicos agregados
            </td>
          </tr>
          <tr v-if="loading">
            <td colspan="6" class="px-4 py-6">
              <div class="animate-pulse space-y-3">
                <div class="flex gap-4">
                  <div class="h-4 bg-gray-200 rounded w-2/5"></div>
                  <div class="h-4 bg-gray-200 rounded w-1/6"></div>
                  <div class="h-4 bg-gray-200 rounded w-1/5"></div>
                  <div class="h-4 bg-gray-200 rounded w-1/5"></div>
                  <div class="h-4 bg-gray-200 rounded flex-1"></div>
                </div>
                <div class="flex gap-4">
                  <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                  <div class="h-4 bg-gray-200 rounded w-1/5"></div>
                  <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                  <div class="h-4 bg-gray-200 rounded w-1/6"></div>
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
  addPagoTecnico,
  deleteCostoAdicional,
} from '../api/CostosAdicionales'
import type { PagoTecnico, CreatePagoTecnicoPayload } from '../api/CostosAdicionales'

const props = defineProps<{
  entradaId: number
}>()

const emit = defineEmits<{
  changed: []
}>()

// Estado
const pagos = ref<PagoTecnico[]>([])
const tecnicos = ref<any[]>([])
const loading = ref(false)
const adding = ref(false)

const newItem = ref<CreatePagoTecnicoPayload & { tecnico_id: number | null }>({
  descripcion: '',
  tipo_pago: 'fijo',
  valor: 0,
  tecnico_id: null,
  observaciones: '',
})

// Computed
const puedeAgregar = computed(() => {
  return (
    newItem.value.descripcion.trim() !== '' &&
    newItem.value.valor >= 0
  )
})

// Métodos
async function load() {
  try {
    loading.value = true
    const costos = await fetchCostosAdicionales(props.entradaId)
    pagos.value = costos.pagos_tecnicos
  } catch (e: any) {
    console.error('Error cargando pagos:', e)
    toast.error('No se pudieron cargar los pagos')
  } finally {
    loading.value = false
  }
}

async function cargarTecnicos() {
  try {
    const { data } = await http.get('/users', {
      params: { per_page: 1000 },
    })
    tecnicos.value = data.data || data || []
  } catch (e) {
    console.error('Error cargando técnicos:', e)
  }
}

async function agregar() {
  if (!puedeAgregar.value) {
    toast.warning('Completa todos los campos requeridos')
    return
  }

  try {
    adding.value = true
    await addPagoTecnico(props.entradaId, {
      descripcion: newItem.value.descripcion,
      tipo_pago: newItem.value.tipo_pago,
      valor: newItem.value.valor,
      tecnico_id: newItem.value.tecnico_id || undefined,
      observaciones: newItem.value.observaciones || undefined,
    })

    await load()

    // Resetear formulario
    newItem.value = {
      descripcion: '',
      tipo_pago: 'fijo',
      valor: 0,
      tecnico_id: null,
      observaciones: '',
    }

    toast.success('Pago agregado correctamente')
    emit('changed')
  } catch (e: any) {
    console.error('Error agregando pago:', e)
    toast.error(e?.response?.data?.message || 'No se pudo agregar el pago')
  } finally {
    adding.value = false
  }
}

async function eliminar(id: number) {
  if (!confirm('¿Eliminar este pago?')) return

  try {
    await deleteCostoAdicional(props.entradaId, 'pago-tecnico', id)
    await load()
    toast.success('Pago eliminado')
    emit('changed')
  } catch (e: any) {
    console.error('Error eliminando pago:', e)
    toast.error('No se pudo eliminar el pago')
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
  cargarTecnicos()
})

watch(
  () => props.entradaId,
  (id) => {
    if (id) load()
  },
  { immediate: true }
)
</script>