<template>
  <div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h4 class="text-sm font-medium text-gray-700">
        Repuestos externos
      </h4>
    </div>

    <!-- Formulario nuevo repuesto externo -->
    <div class="p-3 border rounded-lg bg-gray-50">
      <div class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Descripción *</label>
          <input v-model="form.descripcion" type="text" class="w-full input" placeholder="Ej: Pantalla genérica..." />
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Cantidad *</label>
          <input v-model.number="form.cantidad" type="number" min="1" class="w-full input" />
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Costo unitario *</label>
          <input v-model.number="form.costo_unitario" type="number" min="0" step="100" class="w-full input" />
        </div>
        <!-- Proveedor -->
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Proveedor</label>
          <select v-model="form.proveedor_id" class="w-full input">
            <option value="" disabled>Selecciona un proveedor</option>
            <option v-for="p in proveedores" :key="p.id" :value="p.id">
              {{ p.nombre }}
            </option>
          </select>
        </div>
        <div>
          <button @click="agregar" :disabled="adding" class="btn btn-primary w-full">
            {{ adding ? 'Agregando…' : 'Agregar' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Tabla repuestos externos -->
    <div class="overflow-x-auto border rounded-lg">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Descripción</th>
            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Cantidad</th>
            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Costo unitario</th>
            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Costo total</th>
            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in repuestos" :key="r.id" class="border-t">
            <td class="px-3 py-2 text-sm">{{ r.descripcion }}</td>
            <td class="px-3 py-2 text-sm">{{ r.cantidad }}</td>
            <td class="px-3 py-2 text-sm">{{ money(r.costo_unitario) }}</td>
            <td class="px-3 py-2 text-sm">{{ money(r.costo_total) }}</td>
            <td class="px-3 py-2 text-right">
              <button class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200"
                      @click="eliminar(r.id)">
                Eliminar
              </button>
            </td>
          </tr>
          <tr v-if="!loading && repuestos.length === 0">
            <td colspan="5" class="px-3 py-6 text-center text-sm text-gray-500">
              Aún no hay repuestos externos.
            </td>
          </tr>
          <tr v-if="loading">
            <td colspan="5" class="px-3 py-6 text-center text-sm text-gray-500">
              Cargando repuestos externos…
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

// APIs
import { fetchRepuestosExternos, createRepuestoExterno, deleteRepuestoExterno } from '../api/repuestosExternos'
import { fetchProveedores, type Proveedor } from '../api/proveedores'

// Types
import type { RepuestoExterno, CreateRepuestoExternoPayload } from '../types/repuestoExterno'

const props = defineProps<{ clienteId: number, ordenId: number, equipoId: number }>()
const emit = defineEmits<{ (e: 'changed'): void, (e: 'total-changed', total: number): void }>()

// Estado principal
const repuestos = ref<RepuestoExterno[]>([])
const proveedores = ref<Proveedor[]>([])

const form = ref<CreateRepuestoExternoPayload>({
  descripcion: '',
  cantidad: 1,
  costo_unitario: 0,
  proveedor_id: null, // obligatorio en el backend
  observaciones: '',
  fecha_gasto: null,
})

const loading = ref(false)
const adding = ref(false)

// ====== Cargar proveedores y repuestos ======
onMounted(async () => {
  try {
    proveedores.value = await fetchProveedores()
  } catch {
    toast.error('No se pudieron cargar los proveedores.')
  }
})

// Cargar lista inicial de repuestos externos
async function load() {
  try {
    loading.value = true
    repuestos.value = await fetchRepuestosExternos(props.clienteId, props.ordenId, props.equipoId)
    calcularTotal()
  } catch {
    toast.error('No se pudieron cargar los repuestos externos.')
  } finally {
    loading.value = false
  }
}
watch(() => props.equipoId, (id) => { if (id) load() }, { immediate: true })

// ====== Crear repuesto externo ======
async function agregar() {
  if (!form.value.proveedor_id) {
    toast.warning('Selecciona un proveedor antes de agregar.')
    return
  }

  try {
    adding.value = true
    await createRepuestoExterno(props.clienteId, props.ordenId, props.equipoId, form.value)
    await load()
    form.value = { descripcion: '', cantidad: 1, costo_unitario: 0, proveedor_id: null, observaciones: '', fecha_gasto: null }
    toast.success('Repuesto externo agregado')
    emit('changed')
  } catch (e: any) {
    toast.error(e?.response?.data?.message || 'No se pudo agregar el repuesto externo.')
  } finally {
    adding.value = false
  }
}

// ====== Eliminar repuesto externo ======
async function eliminar(id: number) {
  try {
    await deleteRepuestoExterno(props.clienteId, props.ordenId, props.equipoId, id)
    await load()
    toast.success('Repuesto externo eliminado')
    emit('changed')
  } catch {
    toast.error('No se pudo eliminar el repuesto externo.')
  }
}

// ====== Calcular total ======
function calcularTotal() {
  const total = repuestos.value.reduce((acc, r) => acc + Number(r.costo_total), 0)
  emit('total-changed', total)
}

// ====== Utils ======
const money = (n: number) =>
  n.toLocaleString('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 })
</script>

<style scoped>
.input { @apply px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500; }
.btn { @apply px-3 py-2 rounded-lg transition-colors; }
.btn-primary { @apply bg-violet-600 text-white hover:bg-violet-700 disabled:opacity-60; }
</style>