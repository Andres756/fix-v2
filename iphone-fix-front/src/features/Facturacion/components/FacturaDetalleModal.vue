<template>
  <Teleport to="body">
    <transition name="fade">
      <div v-if="open" class="fixed inset-0 z-[9999] bg-black/60 flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden">
          <!-- Header -->
          <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
              Factura Detallada
              <span
                v-if="factura?.codigo"
                class="text-sm font-mono bg-gray-100 text-gray-700 px-3 py-1 rounded-lg"
              >
                {{ factura.codigo }}
              </span>
            </h2>
            <button
              @click="emit('close')"
              class="p-2 text-gray-500 hover:bg-gray-100 rounded-full"
              title="Cerrar"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Loading -->
          <div v-if="loading" class="p-10 text-center text-gray-500">
            <div class="w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mx-auto mb-3"></div>
            Cargando información de la factura...
          </div>

          <!-- Contenido -->
          <div v-else-if="factura" class="p-6 space-y-6">
            <!-- Info General -->
            <div class="bg-gray-50 rounded-lg p-4 grid grid-cols-2 gap-4 text-sm">
              <div>
                <p class="text-gray-500">Código</p>
                <p class="font-semibold">{{ factura.codigo }}</p>
              </div>
              <div>
                <p class="text-gray-500">Cliente</p>
                <p class="font-semibold">{{ factura.cliente?.nombre }}</p>
              </div>
              <div>
                <p class="text-gray-500">Estado</p>
                <p class="font-semibold">{{ factura.estado?.nombre }}</p>
              </div>
              <div>
                <p class="text-gray-500">Total</p>
                <p class="font-semibold">{{ formatMoney(factura.total) }}</p>
              </div>
              <div>
                <p class="text-gray-500">Fecha emisión</p>
                <p class="font-semibold">{{ formatDate(factura.fecha_emision) }}</p>
              </div>
              <div>
                <p class="text-gray-500">Saldo pendiente</p>
                <p class="font-semibold text-orange-600">
                  {{ formatMoney(factura.saldo_pendiente) }}
                </p>
              </div>
            </div>

            <!-- Tabla de Detalles -->
            <div>
              <h3 class="text-md font-semibold text-gray-900 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M9 12h6m-6 4h6m-9-8h12M4 6h16M4 18h16" />
                </svg>
                Detalles de Factura
              </h3>

              <div v-if="factura.detalles?.length" class="border border-gray-200 rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                  <thead class="bg-gray-100 text-gray-600">
                    <tr>
                      <th class="text-left px-4 py-2">Descripción</th>
                      <th class="text-center px-4 py-2">Cant.</th>
                      <th class="text-right px-4 py-2">Precio Unit.</th>
                      <th class="text-right px-4 py-2">Descuento</th>
                      <th class="text-right px-4 py-2">Total</th>
                      <th class="text-center px-4 py-2">Entregado</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="d in factura.detalles" :key="d.id" 
                        :class="{
                          'opacity-60 line-through text-gray-500 bg-gray-50': d.estado?.codigo === 'ANUL',
                          'bg-green-50': estaEntregado(d)
                        }" 
                        class="border-t border-gray-100">
                      <td class="px-4 py-2">
                        {{ d.descripcion }}
                        <span v-if="estaEntregado(d)" class="ml-2 text-xs font-semibold text-green-600">
                          ✓ Entregado
                        </span>
                      </td>
                      <td class="px-4 py-2 text-center">{{ d.cantidad }}</td>
                      <td class="px-4 py-2 text-right">{{ formatMoney(d.valor_unitario || d.precio_unitario || 0) }}</td>
                      
                      <!-- ✅ NUEVA COLUMNA: Descuento -->
                      <td class="px-4 py-2 text-right">
                        <span v-if="d.descuento > 0" class="text-red-600">
                          - {{ formatMoney(d.descuento) }}
                        </span>
                        <span v-else class="text-gray-400">—</span>
                      </td>
                      
                      <td class="px-4 py-2 text-right font-semibold">{{ formatMoney(d.total) }}</td>

                      <td class="px-4 py-2 text-center">
                        <input
                          type="checkbox"
                          :checked="estaEntregado(d)"
                          :disabled="
                            factura.estado?.codigo === 'ANUL' || 
                            d.estado?.codigo === 'ANUL' || 
                            estaEntregado(d)
                          "
                          class="accent-green-600 w-4 h-4 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                          @change="handleEntregaChange(d, $event)" 
                        />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div v-else class="text-center text-sm text-gray-500 py-4">
                No hay detalles registrados
              </div>
            </div>

            <!-- Modal de Confirmación de Entrega -->
            <div v-if="modalConfirmacionEntrega.visible" class="fixed inset-0 z-[10000] flex items-center justify-center bg-black/50">
              <div class="bg-white rounded-xl shadow-lg w-96 p-6">
                <h3 class="text-lg font-semibold mb-4">Confirmar entrega</h3>
                <p class="text-sm text-gray-600 mb-6">
                  La factura tiene un saldo pendiente de 
                  <span class="font-bold text-orange-600">{{ formatMoney(factura.saldo_pendiente) }}</span>. 
                  ¿Está seguro de que desea entregar este producto?
                </p>
                <div class="flex justify-end gap-3">
                  <button 
                    @click="cancelarEntrega" 
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300"
                  >
                    No
                  </button>
                  <button 
                    @click="confirmarYEntregar" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                  >
                    Sí, entregar
                  </button>
                </div>
              </div>
            </div>

            <!-- ✅ TOTALES DESGLOSADOS -->
            <div class="flex justify-end mt-4">
              <div class="w-2/5 space-y-2 text-sm">
                <!-- Subtotal sin descuentos -->
                <div class="flex justify-between text-gray-700">
                  <span>Subtotal:</span>
                  <span>{{ formatMoney(factura.subtotal) }}</span>
                </div>
                
                <!-- Descuentos por ítem (suma de todos los detalles) -->
                <div 
                  v-if="factura.detalles?.some((d: any) => d.descuento > 0)" 
                  class="flex justify-between text-red-600"
                >
                  <span>Descuentos por ítem:</span>
                  <span>
                    - {{ formatMoney(factura.detalles.reduce((sum: number, d: any) => sum + (d.descuento || 0), 0)) }}
                  </span>
                </div>
                
                <!-- Descuento global (descuento total - descuentos por ítem) -->
                <div 
                  v-if="(() => {
                    const descuentosItems = factura.detalles?.reduce((sum: number, d: any) => sum + (d.descuento || 0), 0) || 0
                    const descuentoGlobal = (factura.descuentos || 0) - descuentosItems
                    return descuentoGlobal > 0
                  })()" 
                  class="flex justify-between text-red-600"
                >
                  <span>Descuento global:</span>
                  <span>
                    - {{ formatMoney((() => {
                      const descuentosItems = factura.detalles?.reduce((sum: number, d: any) => sum + (d.descuento || 0), 0) || 0
                      return (factura.descuentos || 0) - descuentosItems
                    })()) }}
                  </span>
                </div>
                
                <!-- Total con descuentos -->
                <div class="flex justify-between font-semibold text-gray-900 pt-2 border-t border-gray-300">
                  <span>Total:</span>
                  <span>{{ formatMoney(factura.total) }}</span>
                </div>
              </div>
            </div>

            <!-- Tabla de Pagos -->
            <div>
              <h3 class="text-md font-semibold text-gray-900 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Pagos Registrados
              </h3>

              <div v-if="pagos.length" class="border border-gray-200 rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                  <thead class="bg-gray-100 text-gray-600">
                    <tr>
                      <th class="text-left px-4 py-2">Fecha</th>
                      <th class="text-left px-4 py-2">Forma de pago</th>
                      <th class="text-right px-4 py-2">Monto</th>
                      <th class="text-center px-4 py-2">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="p in pagos" :key="p.id" class="border-t border-gray-100">
                      <td class="px-4 py-2">{{ formatDate(p.fecha) }}</td>
                      <td class="px-4 py-2">{{ p.forma_pago || '—' }}</td>
                      <td class="px-4 py-2 text-right">{{ formatMoney(p.valor) }}</td>
                      <td class="px-4 py-2 text-center">
                        <span
                          v-if="p.estado === 'anulado'"
                          class="text-red-600 font-semibold"
                        >
                          Anulado
                        </span>
                        <button
                          v-else
                          @click="abrirModalAnulacion(p)"
                          class="text-red-600 hover:text-red-800 text-xs font-medium"
                        >
                          Anular
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div v-else class="text-center text-sm text-gray-500 py-6">
                No hay pagos registrados
              </div>
            </div>
          </div>
        </div>

        <!-- Modal de anulación de pago -->
        <div
          v-if="modalAnulacion"
          class="fixed inset-0 bg-black/50 flex items-center justify-center z-[10001]"
        >
          <div class="bg-white rounded-xl shadow-lg w-full max-w-sm p-6">
            <h3 class="text-lg font-semibold mb-4">Anular pago</h3>

            <label class="block text-sm font-medium mb-2">Motivo de anulación</label>
            <select
              v-model="motivoSeleccionado"
              class="w-full border rounded-lg px-3 py-2 mb-4"
            >
              <option disabled value="">Selecciona un motivo</option>
              <option v-for="m in motivos" :key="m.id" :value="m.id">
                {{ m.nombre }}
              </option>
            </select>

            <div class="flex justify-end gap-3">
              <button
                @click="modalAnulacion = false"
                class="px-4 py-2 rounded-lg bg-gray-200 text-gray-800"
              >
                Cancelar
              </button>
              <button
                @click="confirmarAnulacion"
                class="px-4 py-2 rounded-lg bg-red-600 text-white"
                :disabled="!motivoSeleccionado"
              >
                Confirmar
              </button>
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
import { getFactura, fetchPagosFactura, fetchMotivosAnulacion, anularPagoFactura, entregarFactura } from '../api/facturacion'

// Props
const props = defineProps<{
  open: boolean
  facturaId: number | null
}>()

const emit = defineEmits<{ 
  (e: 'close'): void
  (e: 'updated'): void
}>()

// Refs
const factura = ref<any>(null)
const pagos = ref<any[]>([])
const loading = ref(false)

// 🔻 Modal de anulación de pago
const modalAnulacion = ref(false)
const motivos = ref<any[]>([])
const motivoSeleccionado = ref<number | null>(null)
const pagoActual = ref<any>(null)

// 🔻 Modal de confirmación de entrega
const modalConfirmacionEntrega = ref<{
  visible: boolean
  detalleId: number | null
  nuevoEstado: boolean
  yaConfirmado: boolean
}>({
  visible: false,
  detalleId: null,
  nuevoEstado: false,
  yaConfirmado: false
})

// 🧾 Formatos
const formatMoney = (val: number) =>
  new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0
  }).format(val || 0)

const formatDate = (dateStr: string) =>
  dateStr ? new Date(dateStr).toLocaleDateString('es-CO') : '—'

// 🔧 Helper para normalizar el valor de entregado
const estaEntregado = (detalle: any): boolean => {
  const val = detalle?.entregado
  return val === 1 || val === '1' || val === true || val === 'true'
}

// 🔄 Reaccionar a apertura del modal principal
watch(() => props.open, async (isOpen) => {
  if (isOpen && props.facturaId) {
    await cargarFactura(props.facturaId)
  }
})

// 📦 Cargar factura y pagos
async function cargarFactura(id: number) {
  try {
    loading.value = true
    const data = await getFactura(id)
    factura.value = data

    const pagosData = await fetchPagosFactura(id)
    pagos.value = pagosData.data || []
  } catch (e: any) {
    toast.error('Error cargando detalles de la factura')
    console.error(e)
  } finally {
    loading.value = false
  }
}

// 💬 Abrir modal de anulación de pago
async function abrirModalAnulacion(pago: any) {
  pagoActual.value = pago
  motivoSeleccionado.value = null
  modalAnulacion.value = true

  // Cargar motivos solo una vez
  if (!motivos.value.length) {
    try {
      const res = await fetchMotivosAnulacion()
      motivos.value = res.data
    } catch (err) {
      toast.error('Error cargando motivos de anulación')
      console.error(err)
    }
  }
}

async function confirmarAnulacion() {
  if (!pagoActual.value || !motivoSeleccionado.value) return

  try {
    await anularPagoFactura(pagoActual.value.id, motivoSeleccionado.value)
    toast.success('Pago anulado correctamente')
    modalAnulacion.value = false
    await cargarFactura(factura.value.id)
    emit('updated')
  } catch (error: any) {
    toast.error('Error al anular el pago')
    console.error(error)
  }
}

// 📦 Manejo de entrega de productos
function handleEntregaChange(detalle: any, event: Event) {
  const checkbox = event.target as HTMLInputElement
  const nuevoEstado = checkbox.checked

  // Si la factura está anulada, no hacer nada
  if (factura.value.estado?.codigo === 'ANUL') {
    toast.warning('No se puede entregar una factura anulada')
    return
  }

  // Si el detalle ya está entregado, no permitir cambios
  if (estaEntregado(detalle)) {
    checkbox.checked = true // Mantener marcado
    toast.info('Este producto ya fue entregado')
    return
  }

  // Prevenir el cambio inmediato del checkbox
  checkbox.checked = estaEntregado(detalle)

  // Si el usuario está desmarcando (intentando "desmarcar" la entrega), no permitirlo
  if (!nuevoEstado) {
    toast.warning('No se puede desmarcar una entrega')
    return
  }

  // Si la factura tiene saldo pendiente, abrir modal de confirmación
  if (factura.value.saldo_pendiente > 0) {
    modalConfirmacionEntrega.value = {
      visible: true,
      detalleId: detalle.id,
      nuevoEstado,
      yaConfirmado: false
    }
  } else {
    // Si está pagada, entregar directamente sin confirmación
    entregarDirecto(detalle.id)
  }
}

// Entregar sin confirmación (factura pagada)
async function entregarDirecto(detalleId: number) {
  const payload = {
    entregas: [{ detalle_id: detalleId }]
  }

  try {
    await entregarFactura(factura.value.id, payload)
    toast.success('Producto entregado correctamente')
    await cargarFactura(factura.value.id)
    emit('updated')
  } catch (error: any) {
    toast.error(error.response?.data?.message || 'Error al registrar la entrega')
    console.error(error)
  }
}

// Confirmar y proceder con la entrega (con saldo pendiente)
async function confirmarYEntregar() {
  if (!modalConfirmacionEntrega.value.detalleId) return

  const payload = {
    entregas: [
      {
        detalle_id: modalConfirmacionEntrega.value.detalleId
      }
    ],
    forzar: true // 👈 Enviar parámetro para forzar la entrega
  }

  try {
    await entregarFactura(factura.value.id, payload)
    
    toast.success('Producto entregado correctamente')
    modalConfirmacionEntrega.value.visible = false
    
    // Recargar datos
    await cargarFactura(factura.value.id)
    emit('updated')
  } catch (error: any) {
    // Si aún retorna 400 (no debería con forzar=true), mostrar error
    if (error.response?.status === 422) {
      toast.error(error.response.data.message || 'No se puede entregar una factura anulada')
    } else {
      toast.error(error.response?.data?.message || 'Error al registrar la entrega')
    }
    modalConfirmacionEntrega.value.visible = false
    console.error(error)
  }
}

// Cancelar la entrega
function cancelarEntrega() {
  modalConfirmacionEntrega.value = {
    visible: false,
    detalleId: null,
    nuevoEstado: false,
    yaConfirmado: false
  }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>