<template>
  <Teleport to="body">
    <transition name="modal" appear>
      <div v-show="open" class="fixed inset-0 z-[9999] overflow-y-auto">
        <!-- Backdrop -->
        <div 
          class="fixed inset-0 bg-gradient-to-br from-black/30 via-black/50 to-black/70 backdrop-blur-md transition-all duration-300" 
          @click="$emit('close')" 
          aria-hidden="true"
        ></div>

        <!-- Container centrado -->
        <div class="flex min-h-full items-center justify-center p-4">
          <!-- Modal -->
          <div
            class="relative w-full max-w-6xl transform rounded-2xl bg-white shadow-2xl ring-1 ring-black/5 
                   transition-all duration-300 ease-out overflow-hidden"
            role="dialog" aria-modal="true" aria-labelledby="modal-title"
          >
            <!-- Header -->
            <div class="sticky top-0 z-20 bg-gradient-to-r from-white to-gray-50/80 backdrop-blur-xl border-b border-gray-100">
              <div class="flex items-center justify-between px-6 py-4">
                <div class="flex items-center space-x-3">
                  <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                  </div>
                  <h3 id="modal-title" class="text-xl font-bold text-gray-900">Nuevo Producto</h3>
                </div>
                <button 
                  class="flex items-center justify-center w-8 h-8 rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all duration-200" 
                  @click="$emit('close')" 
                  aria-label="Cerrar"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Contenido -->
            <div class="max-h-[75vh] overflow-y-auto custom-scrollbar">
              <div class="p-6 lg:p-8">
                <div class="grid gap-8 lg:grid-cols-4">
                  <!-- Columna principal (3/4) -->
                  <div class="lg:col-span-3 space-y-8">
                    <!-- Información básica -->
                    <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-6 border border-gray-100 shadow-sm">
                      <div class="flex items-center space-x-2 mb-6">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <h4 class="text-lg font-semibold text-gray-900">Información básica</h4>
                      </div>
                      
                      <CoreFields
                        v-model="form"
                        :tipos="tipos"
                        :categorias="categorias"
                        :previewUrl="previewUrl"
                        @imagen="onImagen"
                      />
                    </div>

                    <!-- Detalles específicos - SOLO SI HAY TIPO SELECCIONADO -->
                    <transition name="slide-fade" mode="out-in">
                      <div v-if="form.tipo_inventario_id" class="bg-gradient-to-br from-indigo-50 to-white rounded-xl p-6 border border-indigo-100 shadow-sm">
                        <div class="flex items-center space-x-2 mb-6">
                          <div class="w-2 h-2 bg-indigo-500 rounded-full"></div>
                          <h4 class="text-lg font-semibold text-gray-900">Detalles de {{ tipoNombre(form.tipo_inventario_id) }}</h4>
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
                            v-else-if="isRepuesto"
                            key="repuesto"
                            v-model:detalle="detalle_repuesto" 
                          />
                        </transition>
                      </div>
                    </transition>

                    <!-- Nota informativa -->
                    <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
                      <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="flex-1">
                          <p class="text-sm font-medium text-blue-800">📦 El producto se creará con stock en 0</p>
                          <p class="text-sm text-blue-600 mt-1">Después podrás registrar ingresos de inventario para agregar stock y asignar lotes.</p>
                        </div>
                      </div>
                    </div>

                    <!-- Error -->
                    <div v-if="error" class="rounded-lg bg-red-50 border border-red-200 p-4">
                      <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.314 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <p class="text-sm font-medium text-red-800">{{ error }}</p>
                      </div>
                    </div>
                  </div>

                  <!-- Sidebar (1/4) -->
                  <div class="space-y-6">
                    <div class="sticky top-8 space-y-6">
                      <!-- Resumen -->
                      <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                        <div class="flex items-center space-x-2 mb-4">
                          <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                          </svg>
                          <h4 class="font-semibold text-gray-900">Resumen</h4>
                        </div>
                        
                        <div class="space-y-3 text-sm">
                          <div class="flex justify-between items-center py-2 border-b border-blue-100">
                            <span class="text-gray-600">Nombre:</span>
                            <span class="font-medium text-gray-900 truncate ml-2">{{ form.nombre || '—' }}</span>
                          </div>
                          <div class="flex justify-between items-center py-2 border-b border-blue-100">
                            <span class="text-gray-600">Código:</span>
                            <span class="font-medium text-gray-900">{{ form.codigo || '—' }}</span>
                          </div>
                          <div class="flex justify-between items-center py-2 border-b border-blue-100">
                            <span class="text-gray-600">Tipo:</span>
                            <span class="font-medium text-gray-900">{{ tipoNombre(form.tipo_inventario_id) }}</span>
                          </div>
                          <div class="flex justify-between items-center py-2 border-b border-blue-100">
                            <span class="text-gray-600">Stock mínimo:</span>
                            <span class="font-medium text-gray-900">{{ form.stock_minimo || '—' }}</span>
                          </div>
                          <div class="flex justify-between items-center py-2 border-b border-blue-100">
                            <span class="text-gray-600">Precio venta:</span>
                            <span class="font-bold text-green-600">{{ money(form.precio) }}</span>
                          </div>
                          <div class="flex justify-between items-center py-2">
                            <span class="text-gray-600">Precio mayor:</span>
                            <span class="font-bold text-orange-600">{{ money(form.costo_mayor) }}</span>
                          </div>
                        </div>
                      </div>

                      <!-- Progreso -->
                      <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <div class="flex items-center space-x-2 mb-3">
                          <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                          </svg>
                          <span class="text-sm font-medium text-gray-700">Progreso</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                          <div 
                            class="bg-gradient-to-r from-blue-500 to-indigo-500 h-2 rounded-full transition-all duration-500 ease-out"
                            :style="{ width: completionPercentage + '%' }"
                          ></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">{{ completionPercentage }}% completo</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="sticky bottom-0 z-20 bg-gradient-to-r from-white to-gray-50/80 backdrop-blur-xl border-t border-gray-100">
              <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <span>Los campos marcados con * son obligatorios</span>
                </div>
                
                <div class="flex items-center space-x-3">
                  <button 
                    class="px-6 py-2.5 text-gray-700 border border-gray-300 rounded-lg font-medium
                           hover:bg-gray-50 hover:border-gray-400 transition-all duration-200
                           focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" 
                    @click="$emit('close')"
                  >
                    Cancelar
                  </button>
                  <button 
                    class="px-8 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-medium
                           hover:from-blue-700 hover:to-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed
                           transform hover:scale-105 active:scale-95 transition-all duration-200
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                           shadow-lg hover:shadow-xl"
                    :disabled="saving || !canSave" 
                    @click="guardar"
                  >
                    <span v-if="saving" class="flex items-center space-x-2">
                      <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      <span>Guardando...</span>
                    </span>
                    <span v-else>Crear Producto</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import CoreFields from './forms/CoreFields.vue';
import EquipoFields from './forms/EquipoFields.vue';
import ProductoFields from './forms/ProductoFields.vue';
import RepuestoFields from './forms/RepuestoFields.vue';

import {
  createInventario,
  fetchTiposInventarioOptions,
  fetchCategoriasOptions,
} from '../api/inventario';
import type { CreateInventarioPayload } from '../api/inventario';
import type { Option } from '../../../shared/types/common';

const props = defineProps<{ open: boolean }>();
const emit = defineEmits<{ (e: 'close'): void; (e: 'created'): void }>();

// ✅ Estado principal - SIMPLIFICADO (sin estado, proveedor, lote, stock, costo)
const form = ref<CreateInventarioPayload>({
  nombre: '',
  nombre_detallado: '',
  codigo: '',
  tipo_inventario_id: null as any,
  categoria_id: null as any,
  stock_minimo: null as any,
  precio: null as any,
  costo_mayor: null as any,
  tipo_impuesto: null as any,
  valor_impuesto: null as any,
  notas: ''
});

// Detalles por tipo
const detalle_equipo = ref({ 
  imei_1: '', imei_2: '', estado_fisico: '', 
  version_ios: '', almacenamiento: '', color: '' 
});
const detalle_producto = ref({ 
  material: '', compatibilidad: '', tipo_accesorio: '' 
});
const detalle_repuesto = ref({ 
  modelo_compatible: '', tipo_repuesto: '', 
  referencia_fabricante: '', garantia_meses: null as any 
});

// ✅ Opciones - SOLO tipos y categorías
const tipos = ref<Option[]>([]);
const categorias = ref<Option[]>([]);

const idEquipos = computed(() => tipos.value.find(t => (t.nombre || '').toUpperCase() === 'EQUIPOS')?.id ?? null);
const idProductos = computed(() => tipos.value.find(t => (t.nombre || '').toUpperCase() === 'PRODUCTOS')?.id ?? null);
const idRepuestos = computed(() => tipos.value.find(t => (t.nombre || '').toUpperCase() === 'REPUESTOS')?.id ?? null);

const isEquipo = computed(() => form.value.tipo_inventario_id === idEquipos.value);
const isProducto = computed(() => form.value.tipo_inventario_id === idProductos.value);
const isRepuesto = computed(() => form.value.tipo_inventario_id === idRepuestos.value);

// ✅ Progreso ajustado
const completionPercentage = computed(() => {
  const basicFields = ['nombre', 'codigo', 'tipo_inventario_id', 'categoria_id', 'stock_minimo', 'precio', 'costo_mayor'];
  const completedBasic = basicFields.filter(field => {
    const value = form.value[field as keyof typeof form.value];
    return value !== null && value !== undefined && value !== '' && 
           !(typeof value === 'number' && isNaN(value));
  }).length;

  let specificCompleted = 0;
  let specificTotal = 0;

  if (isEquipo.value) {
    specificTotal = 1;
    specificCompleted = detalle_equipo.value.imei_1.trim() ? 1 : 0;
  } else if (isRepuesto.value) {
    specificTotal = 2;
    specificCompleted = (detalle_repuesto.value.modelo_compatible ? 1 : 0) + 
                       (detalle_repuesto.value.tipo_repuesto ? 1 : 0);
  }

  const totalFields = basicFields.length + specificTotal;
  const totalCompleted = completedBasic + specificCompleted;

  return totalFields > 0 ? Math.round((totalCompleted / totalFields) * 100) : 0;
});

// ✅ Validación ajustada
const canSave = computed(() => {
  const basic = form.value.nombre &&
                form.value.codigo &&
                form.value.tipo_inventario_id &&
                form.value.categoria_id &&
                form.value.stock_minimo !== null && form.value.stock_minimo > 0 &&
                form.value.precio !== null && form.value.precio > 0 &&
                form.value.costo_mayor !== null && form.value.costo_mayor > 0;

  if (!basic) return false;

  if (isEquipo.value) return !!detalle_equipo.value.imei_1?.trim();
  if (isRepuesto.value) return !!detalle_repuesto.value.modelo_compatible?.trim();

  return true;
});

// Cargar categorías filtradas
async function loadCategorias() {
  categorias.value = await fetchCategoriasOptions(
    form.value.tipo_inventario_id || undefined
  ).catch(() => []);
  
  if (form.value.categoria_id && 
      !categorias.value.some(c => Number(c.id) === Number(form.value.categoria_id))) {
    form.value.categoria_id = null as any;
  }
}

// Watchers
watch(() => form.value.tipo_inventario_id, async () => {
  // Reset detalles
  detalle_equipo.value = { imei_1: '', imei_2: '', estado_fisico: '', version_ios: '', almacenamiento: '', color: '' };
  detalle_producto.value = { material: '', compatibilidad: '', tipo_accesorio: '' };
  detalle_repuesto.value = { modelo_compatible: '', tipo_repuesto: '', referencia_fabricante: '', garantia_meses: null };
  
  await loadCategorias();
});

// Cargar opciones
onMounted(async () => {
  tipos.value = await fetchTiposInventarioOptions().catch(() => []);
  await loadCategorias();
});

// Imagen
let imagen: File | null = null;
const previewUrl = ref<string | null>(null);

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

// Utilidades
function money(n: any) {
  const num = Number(n ?? 0);
  return new Intl.NumberFormat('es-CO', { 
    style: 'currency', 
    currency: 'COP', 
    maximumFractionDigits: 0 
  }).format(num || 0);
}

function tipoNombre(id?: number | null) {
  if (!id) return '—';
  const t = tipos.value.find(x => Number(x.id) === Number(id));
  return t?.nombre ?? '—';
}

const saving = ref(false);
const error = ref<string | null>(null);

async function guardar() {
  error.value = null;

  const payload: CreateInventarioPayload = {
    ...form.value,
    ...(isEquipo.value ? { 
      detalle_equipo: { 
        ...detalle_equipo.value, 
        imei_1: detalle_equipo.value.imei_1.trim() 
      } 
    } : {}),
    ...(isProducto.value ? { detalle_producto: { ...detalle_producto.value } } : {}),
    ...(isRepuesto.value ? { detalle_repuesto: { ...detalle_repuesto.value } } : {}),
  };

  try {
    saving.value = true;
    await createInventario(payload, imagen || undefined);
    showToast('✅ Producto creado con stock en 0. Ahora puedes registrar ingresos de inventario.', 'success');
    emit('created');
    reset();
  } catch (e: any) {
    const errorMessage = e?.response?.data?.message || 'No se pudo guardar el producto.';
    error.value = errorMessage;
    showToast(errorMessage, 'error');
    console.error('Error al crear inventario:', e);
  } finally {
    saving.value = false;
  }
}

function showToast(message: string, type: 'success' | 'error' | 'warning' | 'info' = 'info') {
  if (typeof window !== 'undefined' && (window as any).toast) {
    (window as any).toast(message, { type });
  } else {
    console.log(`Toast [${type}]: ${message}`);
    createSimpleToast(message, type);
  }
}

function createSimpleToast(message: string, type: string) {
  const toast = document.createElement('div');
  toast.className = `fixed top-4 right-4 z-[10000] px-6 py-4 rounded-lg shadow-lg text-white font-medium transform transition-all duration-300 max-w-md`;
  const bgColors = { 
    success: 'bg-green-500', 
    error: 'bg-red-500', 
    warning: 'bg-yellow-500', 
    info: 'bg-blue-500' 
  };
  toast.classList.add(bgColors[type as keyof typeof bgColors] || bgColors.info);
  toast.textContent = message;
  toast.style.transform = 'translateX(100%)';
  document.body.appendChild(toast);
  setTimeout(() => { toast.style.transform = 'translateX(0)'; }, 100);
  setTimeout(() => {
    toast.style.transform = 'translateX(100%)';
    setTimeout(() => { 
      if (document.body.contains(toast)) document.body.removeChild(toast); 
    }, 300);
  }, 4000);
}

function reset() {
  form.value = {
    nombre: '',
    nombre_detallado: '',
    codigo: '',
    tipo_inventario_id: null as any,
    categoria_id: null as any,
    stock_minimo: null as any,
    precio: null as any,
    costo_mayor: null as any,
    tipo_impuesto: null as any,
    valor_impuesto: null as any,
    notas: ''
  };
  detalle_equipo.value = { imei_1: '', imei_2: '', estado_fisico: '', version_ios: '', almacenamiento: '', color: '' };
  detalle_producto.value = { material: '', compatibilidad: '', tipo_accesorio: '' };
  detalle_repuesto.value = { modelo_compatible: '', tipo_repuesto: '', referencia_fabricante: '', garantia_meses: null };
  imagen = null;
  previewUrl.value = null;
  error.value = null;
}
</script>

<style scoped>
.modal-enter-active, .modal-leave-active {
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(-20px);
}

.slide-fade-enter-active {
  transition: all 0.3s cubic-bezier(0.55, 0.055, 0.675, 0.19);
}
.slide-fade-leave-active {
  transition: all 0.2s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}
.slide-fade-enter-from {
  transform: translateX(20px);
  opacity: 0;
}
.slide-fade-leave-to {
  transform: translateX(-20px);
  opacity: 0;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>