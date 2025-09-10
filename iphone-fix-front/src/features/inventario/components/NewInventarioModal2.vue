<template>
  <Teleport to="body">
    <transition name="modal" appear>
      <div v-show="open" class="fixed inset-0 z-[9999] overflow-y-auto">
        <!-- Backdrop con blur -->
        <div
          class="fixed inset-0 bg-gradient-to-br from-black/30 via-black/50 to-black/70 backdrop-blur-md transition-all duration-300"
          @click="$emit('close')"
          aria-hidden="true"
        ></div>

        <!-- Contenedor centrado -->
        <div class="flex min-h-full items-center justify-center p-4">
          <!-- Modal -->
          <div
            class="relative w-full max-w-6xl transform rounded-2xl bg-white shadow-2xl ring-1 ring-black/5 transition-all"
          >
            <!-- Header -->
            <div class="flex items-center justify-between border-b px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-lg bg-indigo-100 text-indigo-600 grid place-content-center">📦</div>
                <h3 class="text-lg font-semibold text-gray-900">Nuevo inventario</h3>
              </div>

              <button
                class="rounded p-2 text-gray-500 hover:bg-gray-100"
                @click="$emit('close')"
                aria-label="Cerrar"
              >
                ✕
              </button>
            </div>

            <!-- Body -->
            <div class="p-6">
              <!-- Progreso -->
              <div class="mb-6">
                <div class="flex items-center justify-between text-sm mb-1">
                  <span class="font-medium text-gray-700">Progreso</span>
                  <span class="text-gray-500">{{ completionPercentage }}%</span>
                </div>
                <div class="h-2 rounded-full bg-gray-100">
                  <div
                    class="h-2 rounded-full bg-indigo-500 transition-all"
                    :style="{ width: `${completionPercentage}%` }"
                  ></div>
                </div>
              </div>

              <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Core (2/3) -->
                <div class="lg:col-span-2 space-y-6">
                  <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-6">
                      <div class="h-2 w-2 rounded-full bg-indigo-500"></div>
                      <h4 class="text-lg font-semibold text-gray-900">Información básica</h4>
                    </div>

                    <CoreFields
                      v-model="form"
                      :tipos="tipos"
                      :categorias="categorias"
                      :estados="estados"
                      :proveedores="proveedores"
                      :lotes="lotes"
                      @imagen="onImagen"
                    />
                  </div>

                  <!-- Detalles específicos -->
                  <div class="rounded-xl border border-indigo-100 bg-gradient-to-br from-indigo-50 to-white p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-6">
                      <div class="h-2 w-2 rounded-full bg-indigo-500"></div>
                      <h4 class="text-lg font-semibold text-gray-900">Detalles específicos</h4>
                    </div>

                    <transition name="slide-fade" mode="out-in">
                      <EquipoFields
                        v-if="isEquipo"
                        key="equipo"
                        v-model:detalle="detalle_equipo"
                      />
                      <ProductoFields
                        v-else-if="isProducto"
                        key="producto"
                        v-model:detalle="detalle_producto"
                      />
                      <RepuestoFields
                        v-else
                        key="repuesto"
                        v-model:detalle="detalle_repuesto"
                      />
                    </transition>
                  </div>
                </div>

                <!-- Aside (1/3) -->
                <div class="space-y-6">
                  <!-- Preview de imagen -->
                  <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-4">
                      <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                      <h4 class="text-base font-semibold text-gray-900">Imagen seleccionada</h4>
                    </div>

                    <div v-if="previewUrl" class="relative">
                      <img
                        :src="previewUrl"
                        alt="Preview"
                        class="w-full rounded-lg border object-cover"
                      />
                      <button
                        class="mt-3 w-full rounded-lg border px-3 py-2 text-sm hover:bg-gray-50"
                        @click="clearImagen"
                        type="button"
                      >
                        Quitar imagen
                      </button>
                    </div>
                    <p v-else class="text-sm text-gray-500">Aún no has seleccionado una imagen.</p>
                  </div>

                  <!-- Tip -->
                  <div class="rounded-xl border border-amber-100 bg-amber-50 p-4 text-amber-800">
                    <p class="text-sm">
                      Asegúrate de completar los campos obligatorios para habilitar el botón <b>Guardar</b>.
                    </p>
                  </div>
                </div>
              </div>

              <!-- Error -->
              <p v-if="error" class="mt-4 rounded-md bg-red-50 p-3 text-sm text-red-700 border border-red-200">
                {{ error }}
              </p>
            </div>

            <!-- Footer -->
            <div class="flex flex-col gap-3 border-t px-6 py-4 sm:flex-row sm:justify-end">
              <button
                class="rounded-lg border px-4 py-2 text-gray-700 hover:bg-gray-50"
                @click="$emit('close')"
                type="button"
              >
                Cancelar
              </button>

              <button
                class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 font-medium text-white shadow-lg hover:shadow-xl disabled:opacity-60 disabled:cursor-not-allowed"
                :disabled="saving || !canSave"
                @click="guardar"
                type="button"
              >
                <span v-if="saving" class="flex items-center gap-2">
                  <svg class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                  </svg>
                  Guardando...
                </span>
                <span v-else>Guardar</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import CoreFields from './forms/CoreFields.vue';
import EquipoFields from './forms/EquipoFields.vue';
import ProductoFields from './forms/ProductoFields.vue';
import RepuestoFields from './forms/RepuestoFields.vue';

import {
  createInventario,
  fetchTiposInventarioOptions,
  fetchEstadosInventarioOptions,
  fetchCategoriasOptions,
  fetchProveedoresOptions,
  fetchLotesOptions,
} from '../api/inventario';
import type { CreateInventarioPayload } from '../api/inventario';

/* Props / Emits */
const props = defineProps<{ open: boolean }>();
const emit = defineEmits<{ (e: 'close'): void; (e: 'created'): void }>();

/* State */
const saving = ref(false);
const error = ref<string | null>(null);

const form = ref<any>({
  nombre: '',
  nombre_detallado: '',
  codigo: '',
  tipo_inventario_id: null,
  categoria_id: null,
  estado_inventario_id: null,
  proveedor_id: null,
  lote_id: null,
  stock: null,
  stock_minimo: null,
  precio: null as any,
  costo: null as any,
  tipo_impuesto: null as any, // 'n/a' | 'porcentaje' | 'fijo'
  valor_impuesto: null as any,
  notas: '',
});

const detalle_equipo = ref({
  imei_1: '',
  imei_2: '',
  estado_fisico: '',
  version_ios: '',
  almacenamiento: '',
  color: '',
});
const detalle_producto = ref({
  material: '',
  compatibilidad: '',
  tipo_accesorio: '',
});
const detalle_repuesto = ref({
  modelo_compatible: '',
  tipo_repuesto: '',
  referencia_fabricante: '',
  garantia_meses: null as any,
});

let imagen: File | null = null;
const previewUrl = ref<string | null>(null);

/* Options */
const tipos = ref<any[]>([]);
const categorias = ref<any[]>([]);
const estados = ref<any[]>([]);
const proveedores = ref<any[]>([]);
const lotes = ref<any[]>([]);

onMounted(async () => {
  try {
    const [tip, cat, est, prov, lot] = await Promise.all([
      fetchTiposInventarioOptions(),
      fetchCategoriasOptions(),
      fetchEstadosInventarioOptions(),
      fetchProveedoresOptions(),
      fetchLotesOptions(),
    ]);
    tipos.value = tip;
    categorias.value = cat;
    estados.value = est;
    proveedores.value = prov;
    lotes.value = lot;
  } catch (e) {
    // Silencioso
  }
});

/* Tipos dinámicos por nombre */
const idEquipos = computed(() => tipos.value.find(t => (t.nombre || '').toUpperCase() === 'EQUIPOS')?.id ?? null);
const idProductos = computed(() => tipos.value.find(t => (t.nombre || '').toUpperCase() === 'PRODUCTOS')?.id ?? null);
const idRepuestos = computed(() => tipos.value.find(t => (t.nombre || '').toUpperCase() === 'REPUESTOS')?.id ?? null);

const isEquipo = computed(() => form.value.tipo_inventario_id === idEquipos.value);
const isProducto = computed(() => form.value.tipo_inventario_id === idProductos.value);
const isRepuesto = computed(() => form.value.tipo_inventario_id === idRepuestos.value);

/* Progreso */
const completionPercentage = computed(() => {
  const basicFields = ['nombre', 'tipo_inventario_id', 'categoria_id', 'estado_inventario_id', 'proveedor_id', 'precio', 'costo'];
  const completedBasic = basicFields.filter(f => {
    const v = (form.value as any)[f];
    return v !== null && v !== undefined && v !== '' && !(typeof v === 'number' && Number.isNaN(v));
  }).length;

  let specificTotal = 0;
  let specificCompleted = 0;

  if (isEquipo.value) {
    const required = ['imei_1'];
    specificTotal = required.length;
    specificCompleted = required.filter(f => (detalle_equipo.value as any)[f]?.toString().trim()).length;
  } else if (isProducto.value) {
    specificTotal = 0;
    specificCompleted = 0;
  } else if (isRepuesto.value) {
    const required = ['modelo_compatible', 'tipo_repuesto'];
    specificTotal = required.length;
    specificCompleted = required.filter(f => (detalle_repuesto.value as any)[f]?.toString().trim()).length;
  }

  const totalFields = basicFields.length + specificTotal;
  const totalCompleted = completedBasic + specificCompleted;
  if (totalFields === 0) return 0;
  return Math.round((totalCompleted / totalFields) * 100);
});

/* Validación mínima para habilitar Guardar */
const canSave = computed(() => {
  const b = form.value;
  const basic =
    b.nombre &&
    b.tipo_inventario_id &&
    b.categoria_id &&
    b.estado_inventario_id &&
    b.proveedor_id &&
    b.precio !== null &&
    b.costo !== null;

  if (!basic) return false;

  if (isEquipo.value) return !!detalle_equipo.value.imei_1?.trim();
  if (isRepuesto.value) return !!detalle_repuesto.value.modelo_compatible?.trim() && !!detalle_repuesto.value.tipo_repuesto?.trim();

  return true;
});

/* Imagen */
function onImagen(file: File | null) {
  imagen = file ?? null;
  if (previewUrl.value) {
    URL.revokeObjectURL(previewUrl.value);
    previewUrl.value = null;
  }
  if (imagen) {
    previewUrl.value = URL.createObjectURL(imagen);
  }
}
function clearImagen() {
  onImagen(null);
}

/* Guardar */
async function guardar() {
  error.value = null;

  const payload: CreateInventarioPayload = {
    ...form.value,
    ...(isEquipo.value ? { detalle_equipo: { ...detalle_equipo.value, imei_1: detalle_equipo.value.imei_1.trim() } } : {}),
    ...(isProducto.value ? { detalle_producto: { ...detalle_producto.value } } : {}),
    ...(isRepuesto.value ? { detalle_repuesto: { ...detalle_repuesto.value } } : {}),
  };

  try {
    saving.value = true;
    await createInventario(payload, imagen || undefined);
    emit('created');
    reset();
  } catch (e: any) {
    error.value = e?.response?.data?.message || 'No se pudo guardar el inventario.';
  } finally {
    saving.value = false;
  }
}

function reset() {
  form.value = {
    nombre: '',
    nombre_detallado: '',
    codigo: '',
    tipo_inventario_id: null,
    categoria_id: null,
    estado_inventario_id: null,
    proveedor_id: null,
    lote_id: null,
    stock: null,
    stock_minimo: null,
    precio: null,
    costo: null,
    tipo_impuesto: null,
    valor_impuesto: null,
    notas: '',
  };
  detalle_equipo.value = { imei_1: '', imei_2: '', estado_fisico: '', version_ios: '', almacenamiento: '', color: '' };
  detalle_producto.value = { material: '', compatibilidad: '', tipo_accesorio: '' };
  detalle_repuesto.value = { modelo_compatible: '', tipo_repuesto: '', referencia_fabricante: '', garantia_meses: null };
  clearImagen();
}
</script>

<style scoped>
.modal-enter-from,
.modal-leave-to { opacity: 0; }
.modal-enter-active,
.modal-leave-active { transition: opacity .2s ease; }

.slide-fade-enter-from { opacity: 0; transform: translateY(6px); }
.slide-fade-enter-active { transition: all .2s ease; }
.slide-fade-leave-to { opacity: 0; transform: translateY(-6px); }
.slide-fade-leave-active { transition: all .15s ease; }
</style>
