<template>
  <Teleport to="body">
    <transition name="modal" appear>
      <div v-show="open" class="fixed inset-0 z-[9999] overflow-y-auto">
        <!-- Backdrop con blur mejorado -->
        <div 
          class="fixed inset-0 bg-gradient-to-br from-black/30 via-black/50 to-black/70 backdrop-blur-md transition-all duration-300" 
          @click="$emit('close')" 
          aria-hidden="true"
        ></div>

        <!-- Container centrado -->
        <div class="flex min-h-full items-center justify-center p-4">
          <!-- Modal mejorada -->
          <div
            class="relative w-full max-w-6xl transform rounded-2xl bg-white shadow-2xl ring-1 ring-black/5 
                   transition-all duration-300 ease-out overflow-hidden"
            role="dialog" aria-modal="true" aria-labelledby="modal-title"
          >
            <!-- Header con gradiente sutil -->
            <div class="sticky top-0 z-20 bg-gradient-to-r from-white to-gray-50/80 backdrop-blur-xl border-b border-gray-100">
              <div class="flex items-center justify-between px-6 py-4">
                <div class="flex items-center space-x-3">
                  <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                  </div>
                  <h3 id="modal-title" class="text-xl font-bold text-gray-900">Nuevo inventario</h3>
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

            <!-- Contenido con scroll mejorado -->
            <div class="max-h-[75vh] overflow-y-auto custom-scrollbar">
              <div class="p-6 lg:p-8">
                <div class="grid gap-8 lg:grid-cols-4">
                  <!-- Columna principal (3/4) -->
                  <div class="lg:col-span-3 space-y-8">
                    <!-- Campos principales con mejor diseño -->
                    <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-6 border border-gray-100 shadow-sm">
                      <div class="flex items-center space-x-2 mb-6">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <h4 class="text-lg font-semibold text-gray-900">Información básica</h4>
                      </div>
                      
                      <CoreFields
                        v-model="form"
                        :tipos="tipos"
                        :categorias="categorias"
                        :estados="estados"
                        :proveedores="proveedores"
                        :lotes="lotes"
                        @imagen="file => (imagen = file)"
                      />
                    </div>

                    <!-- Detalles específicos con transición -->
                    <div class="bg-gradient-to-br from-indigo-50 to-white rounded-xl p-6 border border-indigo-100 shadow-sm">
                      <div class="flex items-center space-x-2 mb-6">
                        <div class="w-2 h-2 bg-indigo-500 rounded-full"></div>
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

                    <!-- Error con mejor estilo -->
                    <div v-if="error" class="rounded-lg bg-red-50 border border-red-200 p-4">
                      <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.314 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <p class="text-sm font-medium text-red-800">{{ error }}</p>
                      </div>
                    </div>
                  </div>

                  <!-- Sidebar mejorado (1/4) -->
                  <div class="space-y-6">
                    <!-- Container sticky para resumen y progreso -->
                    <div class="sticky top-8 space-y-6">
                      <!-- Resumen con mejor diseño -->
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
                            <span class="text-gray-600">Precio:</span>
                            <span class="font-bold text-green-600">{{ money(form.precio) }}</span>
                          </div>
                          <div class="flex justify-between items-center py-2 border-b border-blue-100">
                            <span class="text-gray-600">Costo:</span>
                            <span class="font-bold text-orange-600">{{ money(form.costo) }}</span>
                          </div>
                          <div class="flex justify-between items-center py-2">
                            <span class="text-gray-600">Stock:</span>
                            <span class="font-medium text-gray-900">{{ form.stock ?? '—' }}</span>
                          </div>
                        </div>
                      </div>

                      <!-- Progress indicator -->
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

            <!-- Footer mejorado -->
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
                    <span v-else>Guardar inventario</span>
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

<style scoped>
/* Animaciones de modal */
.modal-enter-active, .modal-leave-active {
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(-20px);
}

/* Animaciones de contenido */
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

/* Scrollbar personalizado */
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

/* Hover effects */
.hover-lift:hover {
  transform: translateY(-2px);
  box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
}
</style>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import CoreFields from './forms/CoreFields.vue';
import EquipoFields from './forms/EquipoFields.vue';
import ProductoFields from './forms/ProductoFields.vue';
import RepuestoFields from './forms/RepuestoFields.vue';

import {
  createInventario,
  fetchTiposInventarioOptions,
  fetchEstadosInventarioOptions,
  fetchCategoriasOptions,   // ← usaremos el filtro por tipo
  fetchProveedoresOptions,
  fetchLotesOptions
} from '../api/inventario';
import type { CreateInventarioPayload } from '../api/inventario';
import type { Option } from '../../../shared/types/common';

const props = defineProps<{ open: boolean }>();
const emit = defineEmits<{ (e: 'close'): void; (e: 'created'): void }>();

// Estado principal
const form = ref<CreateInventarioPayload>({
  nombre: '',
  nombre_detallado: '',            // ← NUEVO
  codigo: '',
  tipo_inventario_id: null as any,
  categoria_id: null as any,
  estado_inventario_id: null as any,
  proveedor_id: null as any,
  lote_id: null,
  stock: null,
  stock_minimo: null,
  precio: null as any,
  costo: null as any,
  costo_mayor: null as any,        // ← NUEVO
  tipo_impuesto: null as any,
  valor_impuesto: null as any,
  notas: ''
});

// Detalles por tipo
const detalle_equipo = ref({ imei_1: '', imei_2: '', estado_fisico: '', version_ios: '', almacenamiento: '', color: '' });
const detalle_producto = ref({ material: '', compatibilidad: '', tipo_accesorio: '' });
const detalle_repuesto = ref({ modelo_compatible: '', tipo_repuesto: '', referencia_fabricante: '', garantia_meses: null as any });

// Opciones
const tipos = ref<Option[]>([]);
const estados = ref<Option[]>([]);
const categorias = ref<Option[]>([]);
const proveedores = ref<Option[]>([]);
const lotes = ref<Option[]>([]);

const idEquipos = computed(() => tipos.value.find(t => (t.nombre || '').toUpperCase() === 'EQUIPOS')?.id ?? null);
const idProductos = computed(() => tipos.value.find(t => (t.nombre || '').toUpperCase() === 'PRODUCTOS')?.id ?? null);
const idRepuestos = computed(() => tipos.value.find(t => (t.nombre || '').toUpperCase() === 'REPUESTOS')?.id ?? null);

const isEquipo = computed(() => form.value.tipo_inventario_id === idEquipos.value);
const isProducto = computed(() => form.value.tipo_inventario_id === idProductos.value);
const isRepuesto = computed(() => form.value.tipo_inventario_id === idRepuestos.value);

// Progreso de completado mejorado
const completionPercentage = computed(() => {
  const basicFields = ['nombre', 'codigo', 'tipo_inventario_id', 'categoria_id', 'estado_inventario_id', 'proveedor_id', 'precio', 'costo'];
  const completedBasic = basicFields.filter(field => {
    const value = form.value[field as keyof typeof form.value];
    return value !== null && value !== undefined && value !== '';
  }).length;

  let specificCompleted = 0;
  let specificTotal = 0;

  if (isEquipo.value) {
    const equipoFields = ['imei_1'];
    specificTotal = equipoFields.length;
    specificCompleted = equipoFields.filter(field => {
      const value = detalle_equipo.value[field as keyof typeof detalle_equipo.value];
      return value !== null && value !== undefined && value !== '';
    }).length;
  } else if (isProducto.value) {
    const productoFields = ['material', 'compatibilidad'];
    specificTotal = productoFields.length;
    specificCompleted = productoFields.filter(field => {
      const value = detalle_producto.value[field as keyof typeof detalle_producto.value];
      return value !== null && value !== undefined && value !== '';
    }).length;
  } else if (isRepuesto.value) {
    const repuestoFields = ['modelo_compatible', 'tipo_repuesto'];
    specificTotal = repuestoFields.length;
    specificCompleted = repuestoFields.filter(field => {
      const value = detalle_repuesto.value[field as keyof typeof detalle_repuesto.value];
      return value !== null && value !== undefined && value !== '';
    }).length;
  }

  const totalFields = basicFields.length + specificTotal;
  const totalCompleted = completedBasic + specificCompleted;

  return Math.round((totalCompleted / totalFields) * 100);
});

// Validación mejorada para habilitar guardar
const canSave = computed(() => {
  const basicValid = form.value.nombre &&
                    form.value.codigo &&
                    form.value.tipo_inventario_id &&
                    form.value.categoria_id &&
                    form.value.estado_inventario_id &&
                    form.value.proveedor_id &&
                    form.value.precio !== null &&
                    form.value.costo !== null;

  let specificValid = true;

  if (isEquipo.value) {
    specificValid = detalle_equipo.value.imei_1.trim() !== '';
  } else if (isProducto.value) {
    specificValid = detalle_producto.value.material !== '' ||
                   detalle_producto.value.compatibilidad !== '';
  } else if (isRepuesto.value) {
    specificValid = detalle_repuesto.value.modelo_compatible !== '' ||
                   detalle_repuesto.value.tipo_repuesto !== '';
  }

  return basicValid && specificValid;
});

// Cargar categorías filtradas por tipo
async function loadCategorias() {
  categorias.value = await fetchCategoriasOptions(form.value.tipo_inventario_id || undefined).catch(() => []);
  // si la categoría actual no está en la lista filtrada, limpiar para evitar FK inválida
  if (form.value.categoria_id && !categorias.value.some(c => Number(c.id ?? (c as any).value) === Number(form.value.categoria_id))) {
    form.value.categoria_id = null as any;
  }
}

// Watchers
watch(isEquipo, eq => {
  if (eq) {
    form.value.stock = 1;
    form.value.stock_minimo = 1;
  }
});

watch(() => form.value.stock, () => {
  if (isEquipo.value) form.value.stock = 1;
});

watch(() => form.value.stock_minimo, () => {
  if (isEquipo.value) form.value.stock_minimo = 1;
});

watch(() => form.value.tipo_inventario_id, async () => {
  // reset de detalles
  detalle_equipo.value = { imei_1: '', imei_2: '', estado_fisico: '', version_ios: '', almacenamiento: '', color: '' };
  detalle_producto.value = { material: '', compatibilidad: '', tipo_accesorio: '' };
  detalle_repuesto.value = { modelo_compatible: '', tipo_repuesto: '', referencia_fabricante: '', garantia_meses: null };

  // recargar categorías por tipo
  await loadCategorias();
});

// Cargar opciones
onMounted(async () => {
  [tipos.value, estados.value] = await Promise.all([
    fetchTiposInventarioOptions().catch(() => []),
    fetchEstadosInventarioOptions(false).catch(() => [])
  ]);

  await loadCategorias(); // ← inicial (si no hay tipo, trae todas o vacío según backend)

  [proveedores.value, lotes.value] = await Promise.all([
    fetchProveedoresOptions().catch(() => []),
    fetchLotesOptions().catch(() => []),
  ]);
});

let imagen: File | null = null;
function onImagen(file: File | null) {  // ← handler para el emit del hijo
  imagen = file;
}

// Utilidades
function money(n: any) {
  const num = Number(n ?? 0);
  return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(num || 0);
}

function tipoNombre(id?: number | null) {
  const key = Number(id);
  const t = (tipos.value as any[]).find(x => Number(x.id ?? x.value) === key);
  return t?.nombre ?? t?.label ?? '—';
}

const saving = ref(false);
const error = ref<string | null>(null);

async function guardar() {
  error.value = null;

  if (isEquipo.value && !detalle_equipo.value.imei_1.trim()) {
    showToast('El IMEI 1 es requerido para equipos', 'error');
    return;
  }

  const payload: CreateInventarioPayload = {
    ...form.value,                                 // incluye nombre_detallado y costo_mayor
    precio: Number(form.value.precio ?? 0),
    costo: Number(form.value.costo ?? 0),
    costo_mayor: form.value.costo_mayor != null ? Number(form.value.costo_mayor) : undefined,
    tipo_impuesto: (form.value.tipo_impuesto ?? 'n/a') as any,
    valor_impuesto:
      form.value.tipo_impuesto && form.value.tipo_impuesto !== 'n/a'
        ? Number(form.value.valor_impuesto ?? 0)
        : undefined,
    ...(isEquipo.value   ? { detalle_equipo:   { ...detalle_equipo.value, imei_1: detalle_equipo.value.imei_1.trim() } } : {}),
    ...(isProducto.value ? { detalle_producto: { ...detalle_producto.value } } : {}),
    ...(isRepuesto.value ? { detalle_repuesto: { ...detalle_repuesto.value } } : {}),
  };

  try {
    saving.value = true;
    await createInventario(payload, imagen || undefined);
    showToast('¡Inventario creado exitosamente!', 'success');
    emit('created');
    reset();
  } catch (e: any) {
    const errorMessage = e?.response?.data?.message || 'No se pudo guardar el inventario.';
    error.value = errorMessage;
    showToast(errorMessage, 'error');
    console.error('Error al crear inventario:', e);
  } finally {
    saving.value = false;
  }
}

// Función para mostrar toasts
function showToast(message: string, type: 'success' | 'error' | 'warning' | 'info' = 'info') {
  if (typeof window !== 'undefined' && (window as any).toast) {
    (window as any).toast(message, { type });
  } else {
    console.log(`Toast [${type}]: ${message}`);
    createSimpleToast(message, type);
  }
}

// Toast simple nativo como fallback
function createSimpleToast(message: string, type: string) {
  const toast = document.createElement('div');
  toast.className = `fixed top-4 right-4 z-[10000] px-6 py-4 rounded-lg shadow-lg text-white font-medium transform transition-all duration-300 max-w-md`;
  const bgColors = { success: 'bg-green-500', error: 'bg-red-500', warning: 'bg-yellow-500', info: 'bg-blue-500' };
  toast.classList.add(bgColors[type as keyof typeof bgColors] || bgColors.info);
  toast.textContent = message;
  toast.style.transform = 'translateX(100%)';
  document.body.appendChild(toast);
  setTimeout(() => { toast.style.transform = 'translateX(0)'; }, 100);
  setTimeout(() => {
    toast.style.transform = 'translateX(100%)';
    setTimeout(() => { if (document.body.contains(toast)) document.body.removeChild(toast); }, 300);
  }, 4000);
}

function reset() {
  form.value = {
    nombre: '',
    nombre_detallado: '',         // ← NUEVO
    codigo: '',
    tipo_inventario_id: null as any,
    categoria_id: null as any,
    estado_inventario_id: null as any,
    proveedor_id: null as any,
    lote_id: null,
    stock: null,
    stock_minimo: null,
    precio: null as any,
    costo: null as any,
    costo_mayor: null as any,     // ← NUEVO
    tipo_impuesto: null as any,
    valor_impuesto: null as any,
    notas: ''
  };
  detalle_equipo.value = { imei_1: '', imei_2: '', estado_fisico: '', version_ios: '', almacenamiento: '', color: '' };
  detalle_producto.value = { material: '', compatibilidad: '', tipo_accesorio: '' };
  detalle_repuesto.value = { modelo_compatible: '', tipo_repuesto: '', referencia_fabricante: '', garantia_meses: null };
  imagen = null;
  error.value = null;
}
</script>