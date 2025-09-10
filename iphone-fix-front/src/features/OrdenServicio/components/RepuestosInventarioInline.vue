<template>
  <div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h4 class="text-sm font-medium text-gray-700">
        Repuestos del equipo
      </h4>
    </div>

    <!-- Form nuevo repuesto -->
    <div class="p-3 border rounded-lg bg-gray-50">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Buscar repuesto</label>
          <input v-model="search" @input="buscarInventario" type="text" class="w-full input" placeholder="Código o nombre..." />
          <ul v-if="resultados.length" class="border mt-1 bg-white shadow rounded">
            <li 
              v-for="item in resultados" 
              :key="item.id" 
              @click="selectInventario(item)" 
              class="px-2 py-1 hover:bg-gray-100 cursor-pointer text-sm"
            >
              {{ item.codigo }} — {{ item.nombre }}
            </li>
          </ul>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Cantidad</label>
          <input type="number" min="1" v-model.number="newItem.cantidad" class="w-full input" />
        </div>
        <div>
          <button @click="agregar" :disabled="adding || !newItem.inventario_id" class="btn btn-primary w-full">
            {{ adding ? 'Agregando…' : 'Agregar repuesto' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Tabla repuestos -->
    <div class="overflow-x-auto border rounded-lg">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Código</th>
            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Nombre</th>
            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Cantidad</th>
            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Costo total</th>
            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in repuestos" :key="r.id" class="border-t">
            <td class="px-3 py-2 text-sm">{{ r.codigo }}</td>
            <td class="px-3 py-2 text-sm">{{ r.nombre }}</td>
            <td class="px-3 py-2 text-sm">{{ r.cantidad }}</td>
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
              Aún no hay repuestos.
            </td>
          </tr>
          <tr v-if="loading">
            <td colspan="5" class="px-3 py-6 text-center text-sm text-gray-500">
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
import 'vue3-toastify/dist/index.css'
import { fetchRepuestosInventario, createRepuestoInventario, deleteRepuestoInventario } from '../api/repuestosInventario'
import type { RepuestoInventario, CreateRepuestoInventarioPayload } from '../types/repuestoInventario'
import http from '../../../shared/api/http'

const props = defineProps<{ clienteId: number, ordenId: number, equipoId: number }>()
const emit = defineEmits<{ (e: 'changed'): void }>()

const repuestos = ref<RepuestoInventario[]>([])
const resultados = ref<any[]>([])
const search = ref('')
const loading = ref(false)
const adding = ref(false)

const newItem = ref<CreateRepuestoInventarioPayload>({
  inventario_id: 0,
  cantidad: 1,
})

async function load() {
  try {
    loading.value = true
    repuestos.value = await fetchRepuestosInventario(props.clienteId, props.ordenId, props.equipoId)
  } catch (e: any) {
    toast.error('No se pudieron cargar los repuestos.')
  } finally {
    loading.value = false
  }
}
watch(() => props.equipoId, (id) => { if (id) load() }, { immediate: true })

async function buscarInventario() {
  if (search.value.length < 2) {
    resultados.value = []
    return
  }
  try {
    const { data } = await http.get('/inventario/repuestos/search', { 
      params: { q: search.value } 
    })
    resultados.value = data
  } catch (e) {
    console.error(e)
    resultados.value = []
  }
}

function selectInventario(item: any) {
  newItem.value.inventario_id = item.id
  resultados.value = []
  search.value = `${item.codigo} — ${item.nombre}`
}

async function agregar() {
  try {
    adding.value = true
    await createRepuestoInventario(props.clienteId, props.ordenId, props.equipoId, newItem.value)
    await load()
    newItem.value = { inventario_id: 0, cantidad: 1 }
    search.value = ''
    toast.success('Repuesto agregado')
    emit('changed')
  } catch (e: any) {
    toast.error(e?.response?.data?.message || 'No se pudo agregar el repuesto.')
  } finally {
    adding.value = false
  }
}

async function eliminar(id: number) {
  try {
    await deleteRepuestoInventario(props.clienteId, props.ordenId, props.equipoId, id)
    await load()
    toast.success('Repuesto eliminado')
    emit('changed')
  } catch (e: any) {
    toast.error('No se pudo eliminar el repuesto.')
  }
}

const money = (n: number) => n.toLocaleString('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 })
</script>

<style scoped>
.input { @apply px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500; }
.btn { @apply px-3 py-2 rounded-lg transition-colors; }
.btn-primary { @apply bg-violet-600 text-white hover:bg-violet-700 disabled:opacity-60; }
</style>
