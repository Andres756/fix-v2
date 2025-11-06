<template>
  <Teleport to="body">
    <transition name="fade">
      <div v-if="open" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-[9999]">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden">
          <!-- Header -->
            
          <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">
            Orden de Servicio {{ orden?.codigo_orden }}
            </h2>
            <button @click="emit('close')" class="p-2 text-gray-500 hover:bg-gray-100 rounded-full">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

        <div v-if="loading" class="p-10 text-center text-gray-500">
        <div class="w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mx-auto mb-3"></div>
        Cargando información de la orden...
        </div>
    <div v-else>
          <!-- Body -->
          <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4 text-sm bg-gray-50 p-4 rounded-lg">
              <div>
                <p class="text-gray-500">Cliente</p>
                <p class="font-semibold">{{ orden?.cliente?.nombre }}</p>
              </div>
              <div>
                <p class="text-gray-500">Estado</p>
                <p class="font-semibold">{{ orden?.estado }}</p>
              </div>
              <div>
                <p class="text-gray-500">Fecha creación</p>
                <p class="font-semibold">{{ formatDate(orden?.created_at) }}</p>
              </div>
              <div>
                <p class="text-gray-500">Fecha entrega</p>
                <p class="font-semibold">{{ orden?.fecha_entrega || 'No definida' }}</p>
              </div>
            </div>

            <!-- Equipos -->
            <div>
              <h3 class="text-md font-semibold text-gray-900 mb-3">Equipos Asociados</h3>
              <div v-if="orden?.equipos?.length" class="border border-gray-200 rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                  <thead class="bg-gray-100 text-gray-600">
                    <tr>
                      <th class="px-4 py-2 text-left">Equipo</th>
                      <th class="px-4 py-2 text-left">Modelo</th>
                      <th class="px-4 py-2 text-left">Estado</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="eq in orden.equipos" :key="eq.id" class="border-t border-gray-100">
                      <td class="px-4 py-2">{{ eq.nombre }}</td>
                      <td class="px-4 py-2">{{ eq.modelo }}</td>
                      <td class="px-4 py-2 font-semibold">{{ eq.estado }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div v-else class="text-center text-sm text-gray-500 py-6">
                No hay equipos registrados en esta orden.
              </div>
            </div>

            <!-- Botón actualizar estado -->
            <div class="text-right">
            <button
                @click="actualizarEstado"
                :disabled="updating || (orden?.estado && orden.estado.toLowerCase() === 'finalizada')"
                class="px-5 py-2 rounded-lg text-white text-sm font-medium transition-all"
                :class="[
                updating
                    ? 'bg-blue-400 cursor-wait'
                    : (orden?.estado?.toLowerCase() === 'finalizada'
                        ? 'bg-gray-400 cursor-not-allowed'
                        : 'bg-blue-600 hover:bg-blue-700')
                ]"
            >
                <span v-if="updating">Actualizando...</span>
                <span v-else-if="orden?.estado?.toLowerCase() === 'finalizada'">Orden Finalizada ✅</span>
                <span v-else>Actualizar Estado</span>
            </button>
            </div>
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
import { fetchOrden, actualizarEstadoOrden } from '../api/orden'

const props = defineProps<{
  open: boolean
  clienteId: number | null
  ordenId: number | null
}>()

const emit = defineEmits<{ (e: 'close'): void; (e: 'updated'): void }>()

const orden = ref<any>(null)
const loading = ref(false)
const updating = ref(false)

const formatDate = (d: string) =>
  d ? new Date(d).toLocaleDateString('es-CO') : '—'

// 🔄 Cargar orden cuando se abre la modal
watch(() => props.open, async (isOpen) => {
  if (isOpen && props.ordenId && props.clienteId) {
    await cargarOrden()
  }
})

// 📦 Función para cargar la orden
async function cargarOrden() {
  try {
    loading.value = true
    const res = await fetchOrden(props.clienteId!, props.ordenId!)
    orden.value = res.data ?? res
  } catch (error) {
    console.error('❌ Error cargando orden:', error)
    toast.error('No se pudo cargar la orden')
  } finally {
    loading.value = false
  }
}

// 🔁 Actualizar estado de la orden
async function actualizarEstado() {
  if (!props.clienteId || !props.ordenId) return
  try {
    updating.value = true
    const res = await actualizarEstadoOrden(props.clienteId, props.ordenId)

    // ✅ Manejo de mensajes según backend
    if (res.estado?.toLowerCase() === 'finalizada') {
      toast.success(res.message || 'Orden finalizada correctamente')
    } else if (res.message?.includes('Existen equipos')) {
      toast.warning(res.message)
    } else if (res.message?.includes('ya se encuentra finalizada')) {
      toast.info(res.message)
    } else {
      toast.success(res.message || 'Estado actualizado')
    }

    // 🔄 Recargar orden en la modal
    await cargarOrden()
    emit('updated')
  } catch (error: any) {
    console.error('❌ Error al actualizar estado:', error)
    toast.error(error.response?.data?.message || 'Error al actualizar el estado')
  } finally {
    updating.value = false
  }
}
</script>
