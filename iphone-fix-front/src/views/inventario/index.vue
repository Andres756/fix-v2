<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 p-4 md:p-6">
    <!-- Header con gradiente y efectos -->
    <div class="mb-8">
      <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl border border-white/20 p-6 md:p-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
          <div class="space-y-2">
            <div class="flex items-center gap-3">
              <div class="h-10 w-10 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 flex items-center justify-center text-white shadow-lg">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
              </div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-blue-900 bg-clip-text text-transparent">
                Inventario
              </h1>
            </div>
            <div class="flex items-center gap-2 text-gray-600">
              <div class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></div>
              <p class="text-sm font-medium">{{ meta?.total ?? 0 }} productos registrados</p>
            </div>
          </div>
          
          <div class="flex flex-wrap gap-3">
            <button 
              class="group relative overflow-hidden px-6 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
              @click="showNew = true"
            >
              <div class="absolute inset-0 bg-gradient-to-r from-blue-700 to-blue-800 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
              <div class="relative flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nuevo producto
              </div>
            </button>
            
            <button 
              class="group px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-700 font-medium hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
              disabled
            >
              <div class="flex items-center gap-2">
                <svg class="h-5 w-5 group-hover:rotate-12 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                </svg>
                Entrada de stock
              </div>
            </button>
            
            <button 
              class="group px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-700 font-medium hover:border-green-300 hover:bg-green-50 hover:text-green-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
              disabled
            >
              <div class="flex items-center gap-2">
                <svg class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exportar CSV
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtros mejorados -->
    <div class="mb-6">
      <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-lg border border-white/20 p-6">
        <div class="flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
          <!-- Buscador con animación -->
          <div class="relative group max-w-md w-full">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl blur opacity-25 group-hover:opacity-40 transition-opacity duration-200"></div>
            <div class="relative">
              <input
                v-model="search"
                type="search"
                placeholder="Buscar por nombre o código..."
                class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 pl-12 pr-10 text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white/90"
                @input="onSearchInput"
              />
              <div class="absolute left-4 top-1/2 -translate-y-1/2">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
              </div>
              <div class="absolute right-4 top-1/2 -translate-y-1/2">
                <kbd class="px-2 py-1 text-xs font-medium text-gray-400 bg-gray-100 rounded">⌘K</kbd>
              </div>
            </div>
          </div>

          <!-- Filtros por tipo -->
          <div class="flex flex-wrap gap-2">
            <button
              class="relative overflow-hidden px-4 py-2 rounded-xl font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
              :class="tipoId === null 
                ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg transform scale-105' 
                : 'bg-white/80 text-gray-700 border-2 border-gray-200 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 hover:scale-105'"
              @click="setTipo(null)"
            >
              <span class="relative z-10">Todos</span>
              <div v-if="tipoId === null" class="absolute inset-0 bg-gradient-to-r from-blue-700 to-blue-800 opacity-0 hover:opacity-100 transition-opacity duration-200"></div>
            </button>
            
            <button
              v-for="t in tipos"
              :key="t.id"
              class="relative overflow-hidden px-4 py-2 rounded-xl font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
              :class="tipoId === t.id 
                ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg transform scale-105' 
                : 'bg-white/80 text-gray-700 border-2 border-gray-200 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 hover:scale-105'"
              @click="setTipo(t.id)"
            >
              <span class="relative z-10">{{ t.nombre }}</span>
              <div v-if="tipoId === t.id" class="absolute inset-0 bg-gradient-to-r from-blue-700 to-blue-800 opacity-0 hover:opacity-100 transition-opacity duration-200"></div>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabla mejorada con estados -->
    <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-lg border border-white/20 overflow-hidden">
      <!-- Loading state con skeleton -->
      <div v-if="loading" class="p-8">
        <div class="animate-pulse space-y-4">
          <div class="h-4 bg-gray-200 rounded w-1/4"></div>
          <div class="space-y-3">
            <div v-for="i in 5" :key="i" class="grid grid-cols-8 gap-4">
              <div class="h-10 bg-gray-200 rounded"></div>
              <div class="h-10 bg-gray-200 rounded"></div>
              <div class="h-10 bg-gray-200 rounded"></div>
              <div class="h-10 bg-gray-200 rounded"></div>
              <div class="h-10 bg-gray-200 rounded"></div>
              <div class="h-10 bg-gray-200 rounded"></div>
              <div class="h-10 bg-gray-200 rounded"></div>
              <div class="h-10 bg-gray-200 rounded"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Error state -->
      <div v-else-if="error" class="p-8 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-4">
          <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Error al cargar inventario</h3>
        <p class="text-sm text-red-600 mb-4">{{ error }}</p>
        <button 
          class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
          @click="reload"
        >
          Reintentar
        </button>
      </div>

      <!-- Empty state -->
      <div v-else-if="items.length === 0" class="p-8 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
          <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-4.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 009.586 13H7"/>
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">No hay productos</h3>
        <p class="text-sm text-gray-600 mb-4">No se encontraron productos con los filtros actuales</p>
        <button 
          class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
          @click="clearFilters"
        >
          Limpiar filtros
        </button>
      </div>

      <!-- Tabla principal -->
      <div v-else class="overflow-x-auto">
        <table class="min-w-full">
          <thead>
            <tr class="bg-gradient-to-r from-gray-50 to-blue-50 border-b border-gray-200">
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Producto</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Código</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Categoría</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stock</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estado</th>
              <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Precio</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr 
              v-for="(it, index) in items" 
              :key="it.id"
              class="group hover:bg-gradient-to-r hover:from-blue-50/50 hover:to-purple-50/50 transition-all duration-200"
              :style="{ animationDelay: `${index * 50}ms` }"
            >
              <!-- Producto -->
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center gap-4">
                  <div class="relative group/img">
                    <img 
                      v-if="it.imagen_url" 
                      :src="it.imagen_url" 
                      alt="" 
                      class="h-12 w-12 object-cover rounded-xl shadow-md group-hover/img:shadow-lg group-hover/img:scale-110 transition-all duration-200" 
                    />
                    <div v-else class="h-12 w-12 bg-gradient-to-br from-gray-200 to-gray-300 rounded-xl flex items-center justify-center">
                      <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                      </svg>
                    </div>
                  </div>
                  <div class="min-w-0 flex-1">
                    <div class="text-sm font-semibold text-gray-900 group-hover:text-blue-900 transition-colors duration-200">
                      {{ it.nombre }}
                    </div>
                    <div v-if="it.nombre_detallado" class="text-xs text-gray-500 mt-1 truncate">
                      {{ it.nombre_detallado }}
                    </div>
                  </div>
                </div>
              </td>

              <!-- Código -->
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <code v-if="it.codigo" class="px-2 py-1 text-xs font-mono bg-gray-100 text-gray-800 rounded-md group-hover:bg-blue-100 group-hover:text-blue-800 transition-colors duration-200">
                    {{ it.codigo }}
                  </code>
                  <span v-else class="text-sm text-gray-400">—</span>
                </div>
              </td>

              <!-- Categoría -->
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {{ it.categoria?.nombre ?? '—' }}
              </td>

              <!-- Stock -->
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center gap-2">
                  <span :class="stockBadgeClass(it)" class="group-hover:scale-105 transition-transform duration-200">
                    {{ num(it.stock) }}
                  </span>
                  <span class="text-xs text-gray-400">/ {{ num(it.stock_minimo) }}</span>
                  <div v-if="num(it.stock) <= num(it.stock_minimo)" class="ml-1">
                    <svg class="h-4 w-4 text-red-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                  </div>
                </div>
              </td>

              <!-- Estado -->
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transition-colors duration-200"
                  :class="(it.estado || it.estado_inventario || it.estado_inventario_id) 
                    ? 'bg-green-100 text-green-800 group-hover:bg-green-200' 
                    : 'bg-gray-100 text-gray-600 group-hover:bg-gray-200'"
                >
                  <div 
                    class="w-2 h-2 rounded-full mr-2"
                    :class="(it.estado || it.estado_inventario || it.estado_inventario_id) 
                      ? 'bg-green-400' 
                      : 'bg-gray-400'"
                  ></div>
                  {{ it.estado?.nombre ?? it.estado_inventario?.nombre ?? estadoNombre(it.estado_inventario_id) }}
                </span>
              </td>

              <!-- Precio -->
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="text-sm font-bold text-gray-900 group-hover:text-blue-900 transition-colors duration-200">
                  {{ currency(it.precio_final ?? it.precio) }}
                </div>
                <div v-if="it.tipo_impuesto && it.tipo_impuesto !== 'n/a'" class="text-xs text-gray-500 mt-1">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800">
                    Imp: {{ it.tipo_impuesto === 'porcentaje'
                      ? num(it.valor_impuesto).toFixed(2) + '%'
                      : currency(it.valor_impuesto) }}
                  </span>
                </div>
              </td>

            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Paginación mejorada -->
    <div v-if="items.length > 0" class="mt-6">
      <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-lg border border-white/20 px-6 py-4">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div class="text-sm text-gray-600">
            Mostrando <span class="font-semibold text-gray-900">{{ ((meta?.current_page ?? 1) - 1) * perPage + 1 }}</span>
            a <span class="font-semibold text-gray-900">{{ Math.min((meta?.current_page ?? 1) * perPage, meta?.total ?? 0) }}</span>
            de <span class="font-semibold text-gray-900">{{ meta?.total ?? 0 }}</span> productos
          </div>
          
          <div class="flex items-center gap-2">
            <button 
              class="group relative overflow-hidden px-4 py-2 rounded-lg border-2 font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
              :class="canPrev 
                ? 'border-gray-200 text-gray-700 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 hover:scale-105' 
                : 'border-gray-100 text-gray-400 cursor-not-allowed'"
              :disabled="!canPrev" 
              @click="goPrev"
            >
              <div class="flex items-center gap-2">
                <svg class="h-4 w-4 group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Anterior
              </div>
            </button>
            
            <div class="flex items-center gap-1">
              <span class="text-sm text-gray-600">Página</span>
              <span class="mx-2 px-3 py-1 text-sm font-semibold bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg">
                {{ meta?.current_page ?? 1 }}
              </span>
              <span class="text-sm text-gray-600">de {{ meta?.last_page ?? 1 }}</span>
            </div>
            
            <button 
              class="group relative overflow-hidden px-4 py-2 rounded-lg border-2 font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
              :class="canNext 
                ? 'border-gray-200 text-gray-700 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 hover:scale-105' 
                : 'border-gray-100 text-gray-400 cursor-not-allowed'"
              :disabled="!canNext" 
              @click="goNext"
            >
              <div class="flex items-center gap-2">
                Siguiente
                <svg class="h-4 w-4 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal: Nuevo inventario -->
    <NewInventarioModal :open="showNew" @close="showNew = false" @created="onCreated" />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import {
  fetchInventario,
  fetchTiposInventarioOptions,
  fetchEstadosInventarioOptions,
} from '../../features/inventario/api/inventario';
import NewInventarioModal from '../../features/inventario/components/NewInventarioModal.vue';

import type { Inventario } from '../../features/inventario/types/inventario';
import type { PaginationMeta } from '../../shared/types/pagination';
import type { Option } from '../../shared/types/common';

const items = ref<Inventario[]>([]);
const meta = ref<PaginationMeta | null>(null);
const tipos = ref<Option[]>([]);
const estados = ref<Option[]>([]);
const loading = ref(false);
const error = ref<string | null>(null);

// UI
const showNew = ref(false);
const search = ref('');
const tipoId = ref<number | null>(null);
const page = ref(1);
const perPage = ref(15);

const num = (v: unknown) => {
  const n = Number(v);
  return Number.isFinite(n) ? n : 0;
};

onMounted(async () => {
  await Promise.all([loadTipos(), loadEstados()]);
  await reload();
});

async function loadTipos() {
  try { tipos.value = await fetchTiposInventarioOptions(); }
  catch { tipos.value = []; }
}
async function loadEstados() {
  try { estados.value = await fetchEstadosInventarioOptions(); }
  catch { estados.value = []; }
}

let searchTimer: number | undefined;
function onSearchInput() {
  window.clearTimeout(searchTimer);
  searchTimer = window.setTimeout(() => {
    page.value = 1;
    reload();
  }, 350);
}
function setTipo(id: number | null) {
  tipoId.value = id;
  page.value = 1;
  reload();
}

const canPrev = computed(() => (meta.value?.current_page ?? 1) > 1);
const canNext = computed(() => (meta.value?.current_page ?? 1) < (meta.value?.last_page ?? 1));
function goPrev() {
  if (!canPrev.value) return;
  page.value = (meta.value!.current_page ?? 2) - 1;
  reload();
}
function goNext() {
  if (!canNext.value) return;
  page.value = (meta.value!.current_page ?? 0) + 1;
  reload();
}

async function reload() {
  loading.value = true;
  error.value = null;
  try {
    const resp = await fetchInventario({
      q: search.value || undefined,
      page: page.value,
      per_page: perPage.value,
      tipo_inventario_id: tipoId.value ?? undefined,
    });
    items.value = Array.isArray(resp.data) ? resp.data : [];
    meta.value = resp.meta ?? null;
  } catch (e: any) {
    error.value = e?.response?.data?.message || 'Error cargando inventario.';
  } finally {
    loading.value = false;
  }
}

// helpers
function tipoNombre(id?: number | string) {
  const key = Number(id);
  return tipos.value.find(t => t.id === key)?.nombre ?? (id != null ? `#${id}` : '—');
}
function estadoNombre(id?: number | string) {
  const key = Number(id);
  return estados.value.find(e => e.id === key)?.nombre ?? (id != null ? `#${id}` : 'N/A');
}
function stockBadgeClass(it: Inventario) {
  const low = num(it.stock_minimo);
  return num(it.stock) <= low
    ? 'inline-flex px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200'
    : 'inline-flex px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200';
}
function currency(v?: number | string) {
  const n = num(v);
  return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(n);
}
function formatDate(s: string | null | undefined) {
  if (!s) return '—';
  try {
    return new Date(s).toLocaleDateString('es-CO', { year: 'numeric', month: 'short', day: '2-digit' });
  } catch { return '—'; }
}
function clearFilters() {
  search.value = '';
  tipoId.value = null;
  page.value = 1;
  reload();
}
function onCreated() {
  showNew.value = false;
  reload();
}
</script>