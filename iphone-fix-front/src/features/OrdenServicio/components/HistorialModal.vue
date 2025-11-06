<template>
  <Teleport to="body">
    <transition name="fade">
      <div v-if="open" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-[9999]">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden">
          <!-- Header -->
          <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">Historial de la Orden</h2>
            <button @click="emit('close')" class="p-2 text-gray-500 hover:bg-gray-100 rounded-full">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Loading -->
          <div v-if="loading" class="p-10 text-center text-gray-500">
            <div class="w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mx-auto mb-3"></div>
            Cargando historial de la orden...
          </div>

          <!-- Tabla -->
          <div v-else class="p-6">
            <div v-if="historial.length" class="overflow-x-auto border border-gray-200 rounded-lg">
              <table class="w-full text-sm">
                <thead class="bg-gray-100 text-gray-600">
                  <tr>
                    <th class="px-4 py-2 text-left">Fecha</th>
                    <th class="px-4 py-2 text-left">Usuario</th> <!-- 👈 NUEVA COLUMNA -->
                    <th class="px-4 py-2 text-left">Estado anterior</th>
                    <th class="px-4 py-2 text-left">Estado nuevo</th>
                    <th class="px-4 py-2 text-left">Descripción</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in historial" :key="item.id" class="border-t border-gray-100">
                    <td class="px-4 py-2">{{ formatDate(item.fecha) }}</td>
                    <td class="px-4 py-2 font-medium text-gray-800">{{ item.usuario || '—' }}</td> <!-- 👈 NUEVO -->
                    <td class="px-4 py-2">{{ item.estado_anterior || '—' }}</td>
                    <td class="px-4 py-2 font-semibold"
                        :class="{
                          'text-green-600': item.estado_nuevo === 'finalizada',
                          'text-yellow-600': item.estado_nuevo === 'pendiente',
                          'text-red-600': item.estado_nuevo === 'anulada'
                        }">
                      {{ item.estado_nuevo || '—' }}
                    </td>
                    <td class="px-4 py-2">{{ item.descripcion || '—' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-else class="text-center text-gray-500 py-8">
              No hay movimientos registrados para esta orden.
            </div>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { toast } from 'vue3-toastify'
import { fetchHistorialOrden } from '../api/orden'

const props = defineProps<{
  open: boolean
  clienteId: number | null
  ordenId: number | null
}>()

const emit = defineEmits<{ (e: 'close'): void }>()

const historial = ref<any[]>([])
const loading = ref(false)

const formatDate = (d: string) =>
  d ? new Date(d).toLocaleString('es-CO', { dateStyle: 'short', timeStyle: 'short' }) : '—'

watch(() => props.open, async (isOpen) => {
  if (isOpen && props.ordenId && props.clienteId) {
    await cargarHistorial()
  }
})

async function cargarHistorial() {
  try {
    loading.value = true
    const res = await fetchHistorialOrden(props.clienteId!, props.ordenId!)
    historial.value = Array.isArray(res.data) ? res.data : res
  } catch (error) {
    console.error('❌ Error cargando historial:', error)
    toast.error('Error al cargar el historial de la orden')
  } finally {
    loading.value = false
  }
}
</script>
