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
                      <th class="text-center px-4 py-2">Cantidad</th>
                      <th class="text-right px-4 py-2">Valor unitario</th>
                      <th class="text-right px-4 py-2">Total</th>
                      <th class="text-center px-4 py-2">Entregado</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="d in factura.detalles" :key="d.id" 
                        :class="{
                          'opacity-60 line-through text-gray-500 bg-gray-50': d.estado?.codigo === 'ANUL'
                        }" 
                        class="border-t border-gray-100">
                      <td class="px-4 py-2">{{ d.descripcion }}</td>
                      <td class="px-4 py-2 text-center">{{ d.cantidad }}</td>
                      <td class="px-4 py-2 text-right">{{ formatMoney(d.valor_unitario) }}</td>
                      <td class="px-4 py-2 text-right font-semibold">{{ formatMoney(d.total) }}</td>

                      <td class="px-4 py-2 text-center">
                        <input
                          type="checkbox"
                          v-model="d.entregado"
                          :disabled="factura.saldo_pendiente > 0 && !confirmarEntrega" 
                          class="accent-green-600 w-4 h-4 cursor-pointer"
                          @change="handleEntregaChange(d)" 
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
            <div v-if="confirmarEntrega" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50">
              <div class="bg-white rounded-xl shadow-lg w-80 p-6">
                <h3 class="text-lg font-semibold mb-4">Confirmar entrega</h3>
                <p class="text-sm mb-4">La factura tiene saldo pendiente. ¿Está seguro de que desea entregar este producto?</p>
                <div class="flex justify-between gap-2">
                  <button @click="confirmarEntrega = false; procederConEntrega()" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                    Sí
                  </button>
                  <button @click="confirmarEntrega = false" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg">
                    No
                  </button>
                </div>
              </div>
            </div>

            <div class="flex justify-end mt-2 text-sm text-gray-700">
              <div class="w-1/3">
                <div class="flex justify-between">
                  <span>Subtotal:</span>
                  <span>{{ formatMoney(factura.subtotal) }}</span>
                </div>
                <div v-if="factura.descuentos > 0" class="flex justify-between">
                  <span>Descuento:</span>
                  <span>-{{ formatMoney(factura.descuentos) }}</span>
                </div>
                <div class="flex justify-between font-semibold">
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
                  <!-- Modal de anulación -->
                  <div
                    v-if="modalAnulacion"
                    class="fixed inset-0 bg-black/50 flex items-center justify-center z-[10000]"
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

                </table>
              </div>
              <div v-else class="text-center text-sm text-gray-500 py-6">
                No hay pagos registrados
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
import { getFactura, fetchPagosFactura, fetchMotivosAnulacion, anularPagoFactura, entregarFactura } from '../api/facturacion'

// Props
const props = defineProps<{
  open: boolean
  facturaId: number | null
}>()

const emit = defineEmits<{ 
  (e: 'close'): void
  (e: 'updated'): void // 👈 Nuevo evento
}>()

// Refs
const factura = ref<any>(null)
const pagos = ref<any[]>([])
const loading = ref(false)

// 🔻 Modal de anulación
const modalAnulacion = ref(false)
const motivos = ref<any[]>([])
const motivoSeleccionado = ref<number | null>(null)
const pagoActual = ref<any>(null)

// 🧾 Formatos
const formatMoney = (val: number) =>
  new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0
  }).format(val || 0)

const formatDate = (dateStr: string) =>
  dateStr ? new Date(dateStr).toLocaleDateString('es-CO') : '—'

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

// 💬 Abrir modal de anulación
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
    await cargarFactura(factura.value.id) // refresca el modal
    emit('updated')  // 👈 AGREGA ESTA LÍNEA
  } catch (error: any) {
    toast.error('Error al anular el pago')
    console.error(error)
  }
}

// Función para manejar el cambio en el checkbox de entrega
function handleEntregaChange(d: any) {
  if (factura.value.saldo_pendiente > 0 && !confirmarEntrega.value) {
    // Si hay saldo pendiente, mostrar el modal de confirmación
    confirmarEntrega.value = true;
  } else {
    // Si no hay saldo pendiente, proceder con la entrega
    procederConEntrega(d);
  }
}

const confirmarEntrega = ref(false); // Modal de confirmación de entrega

// Mostrar modal de confirmación cuando hay saldo pendiente
async function entregarProductos(detalle: any) {
  const payload = {
    entregas: [
      {
        detalle_id: detalle.id,  // ID de cada detalle
      }
    ]
  };

  // Si la factura tiene saldo pendiente, mostrar el modal de confirmación
  if (factura.value.saldo_pendiente > 0) {
    confirmarEntrega.value = true;
    return;
  }

  // Proceder con la entrega
  await procederConEntrega(payload);
}

// Función para proceder con la entrega de productos después de la confirmación
async function procederConEntrega(d: any) {
  const payload = {
    entregas: [
      {
        detalle_id: d.id,  // ID del detalle de la factura
      }
    ]
  };

  try {
    // Llamar a la API para registrar la entrega
    const response = await entregarFactura(factura.value.id, payload);
    toast.success('Producto entregado correctamente');
    await cargarFactura(factura.value.id); // Refrescar los datos después de la entrega
  } catch (error) {
    toast.error('Error al registrar la entrega');
  }
}

// Función para confirmar entrega desde el modal
async function confirmarEntregaModal() {
  const payload = {
    entregas: factura.value.detalles
      .filter(item => item.entregado)  // Solo los ítems entregados
      .map(item => ({
        detalle_id: item.id,  // ID de cada detalle
      }))
  };

  try {
    // Procedemos con la entrega si el usuario confirma
    await procederConEntrega(payload);
    confirmarEntrega.value = false;  // Cerrar modal de confirmación
  } catch (error) {
    toast.error('Error al procesar la entrega');
  }
}

</script>