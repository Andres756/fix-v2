<script setup lang="ts">
import { ref, watch } from "vue";
import { exportarInventario, fetchTiposInventario } from "../api/exportInventario";
import type { InventarioExportFilters, TipoInventario } from "../types/exportInventario";
import { toast } from 'vue3-toastify';

const props = defineProps({
  open: Boolean,
});

const emit = defineEmits(["update:open"]);
const loading = ref(false);
const tiposInventario = ref<TipoInventario[]>([]);

const filters = ref<InventarioExportFilters>({
  tipo_inventario_id: "",
  activo: "",
  filtro_stock: "todos",
  fecha_desde: "",
  fecha_hasta: "",
});

watch(
  () => props.open,
  async (val) => {
    if (val) {
      await loadTiposInventario();
    }
    if (!val) resetForm();
  }
);

async function loadTiposInventario() {
  try {
    tiposInventario.value = await fetchTiposInventario();
  } catch (err) {
    console.error("Error cargando tipos de inventario:", err);
  }
}

const closeModal = () => emit("update:open", false);

function resetForm() {
  filters.value = {
    tipo_inventario_id: "",
    activo: "",
    filtro_stock: "todos",
    fecha_desde: "",
    fecha_hasta: "",
  };
}

async function handleExport() {
  // Validar rango de fechas
  if (filters.value.fecha_desde && filters.value.fecha_hasta) {
    if (new Date(filters.value.fecha_desde) > new Date(filters.value.fecha_hasta)) {
      toast.warning('La fecha desde no puede ser mayor a la fecha hasta');
      return;
    }
  }

  try {
    loading.value = true;
    await exportarInventario({
      ...filters.value,
      tipo_inventario_nombre:
        tiposInventario.value.find(
          (t) => t.id === Number(filters.value.tipo_inventario_id)
        )?.nombre || "inventario",
    });
    closeModal();
    resetForm();
  } catch (err) {
    console.error("Error al generar el reporte:", err);
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <Teleport to="body">
    <Transition name="fade">
      <div
        v-if="props.open"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-[9999]"
        @click.self="closeModal"
      >
        <div
          class="bg-white rounded-xl shadow-2xl w-full max-w-2xl animate-fade-in relative"
        >
          <!-- Header -->
          <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
              </div>
              <div>
                <h2 class="text-xl font-bold text-gray-900">Exportar Reporte de Inventario</h2>
                <p class="text-sm text-gray-500">Configura los filtros para el reporte</p>
              </div>
            </div>
            <button 
              @click="closeModal" 
              class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Body -->
          <div class="p-6 space-y-4 max-h-[calc(100vh-250px)] overflow-y-auto">
            <!-- Tipo de Inventario -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Tipo de Inventario
              </label>
              <select
                v-model="filters.tipo_inventario_id"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
              >
                <option value="">Todos los tipos</option>
                <option 
                  v-for="tipo in tiposInventario" 
                  :key="tipo.id" 
                  :value="tipo.id"
                >
                  {{ tipo.nombre }}
                </option>
              </select>
            </div>

            <!-- Filtro de Stock -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Estado de Stock
              </label>
              <select
                v-model="filters.filtro_stock"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
              >
                <option value="todos">Todos</option>
                <option value="sin_stock">Sin Stock (≤ 0)</option>
                <option value="con_stock">Con Stock (> 0)</option>
                <option value="bajo_minimo">Bajo Mínimo</option>
              </select>
            </div>

            <!-- Info adicional -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
              <div class="flex gap-2">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-blue-800">
                  Los filtros son opcionales. Si no seleccionas ninguno, se exportarán todos los productos.
                </p>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="bg-gray-50 px-6 py-4 border-t flex justify-end gap-3">
            <button
              @click="closeModal"
              type="button"
              class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
            >
              Cancelar
            </button>
            <button
              @click="handleExport"
              type="button"
              :disabled="loading"
              class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium disabled:bg-red-300 disabled:cursor-not-allowed flex items-center gap-2"
            >
              <svg v-if="loading" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              {{ loading ? "Generando..." : "Exportar Excel" }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>