<template>
  <div class="space-y-8">
    <!-- Información básica -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
      <div class="space-y-2">
        <label class="flex items-center text-sm font-semibold text-gray-700">
          <span>Nombre</span><span class="text-red-500 ml-1">*</span>
        </label>
        <input
          v-model.trim="model.nombre"
          type="text"
          class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 placeholder-gray-500 transition-all duration-200 hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
        />
      </div>

      <div class="space-y-2">
        <label class="text-sm font-semibold text-gray-700">Nombre detallado</label>
        <input
          v-model.trim="model.nombre_detallado"
          type="text"
          class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 placeholder-gray-500 transition-all duration-200 hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
        />
      </div>

      <div class="space-y-2">
        <label class="text-sm font-semibold text-gray-700">Código</label>
        <input
          v-model.trim="model.codigo"
          type="text"
          class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 placeholder-gray-500 transition-all duration-200 hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
        />
      </div>

      <div class="space-y-2">
        <label class="flex items-center text-sm font-semibold text-gray-700">
          <span>Tipo de inventario</span><span class="text-red-500 ml-1">*</span>
        </label>
        <select
          v-model="model.tipo_inventario_id"
          class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 transition-all duration-200 hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
        >
          <option :value="null" disabled>Seleccione...</option>
          <option v-for="t in tipos" :key="t.id" :value="t.id">{{ t.nombre }}</option>
        </select>
      </div>

      <div class="space-y-2">
        <label class="flex items-center text-sm font-semibold text-gray-700">
          <span>Categoría</span><span class="text-red-500 ml-1">*</span>
        </label>
        <select
          v-model="model.categoria_id"
          class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 transition-all duration-200 hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
        >
          <option :value="null" disabled>Seleccione...</option>
          <option v-for="c in categorias" :key="c.id" :value="c.id">{{ c.nombre }}</option>
        </select>
      </div>

      <div class="space-y-2">
        <label class="flex items-center text-sm font-semibold text-gray-700">
          <span>Estado</span><span class="text-red-500 ml-1">*</span>
        </label>
        <select
          v-model="model.estado_inventario_id"
          class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 transition-all duration-200 hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
        >
          <option :value="null" disabled>Seleccione...</option>
          <option v-for="e in estados" :key="e.id" :value="e.id">{{ e.nombre }}</option>
        </select>
      </div>

      <div class="space-y-2">
        <label class="flex items-center text-sm font-semibold text-gray-700">
          <span>Proveedor</span><span class="text-red-500 ml-1">*</span>
        </label>
        <select
          v-model="model.proveedor_id"
          class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 transition-all duration-200 hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
        >
          <option :value="null" disabled>Seleccione...</option>
          <option v-for="p in proveedores" :key="p.id" :value="p.id">{{ p.nombre }}</option>
        </select>
      </div>

      <div class="space-y-2">
        <label class="text-sm font-semibold text-gray-700">Lote</label>
        <select
          v-model="model.lote_id"
          class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 transition-all duration-200 hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
        >
          <option :value="null">Sin lote</option>
          <option v-for="l in lotes" :key="l.id" :value="l.id">{{ l.nombre }}</option>
        </select>
      </div>
    </div>

    <!-- Stock / Precio -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
      <div class="space-y-2">
        <label class="text-sm font-semibold text-gray-700">Stock</label>
        <input
          v-model.number="model.stock"
          type="number"
          min="0"
          class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 transition-all duration-200 hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
        />
      </div>

      <div class="space-y-2">
        <label class="text-sm font-semibold text-gray-700">Stock mínimo</label>
        <input
          v-model.number="model.stock_minimo"
          type="number"
          min="0"
          class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 transition-all duration-200 hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
        />
      </div>

      <div class="space-y-2">
        <label class="flex items-center text-sm font-semibold text-gray-700">
          <span>Precio</span><span class="text-red-500 ml-1">*</span>
        </label>
        <input
          v-model.number="model.precio"
          type="number"
          min="0"
          step="0.01"
          inputmode="decimal"
          class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 transition-all duration-200 hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
        />
      </div>

      <div class="space-y-2">
        <label class="flex items-center text-sm font-semibold text-gray-700">
          <span>Costo</span><span class="text-red-500 ml-1">*</span>
        </label>
        <input
          v-model.number="model.costo"
          type="number"
          min="0"
          step="0.01"
          inputmode="decimal"
          class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 transition-all duration-200 hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
        />
      </div>
    </div>

    <!-- Impuestos -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
      <div class="space-y-2">
        <label class="text-sm font-semibold text-gray-700">Tipo de impuesto</label>
        <select
          v-model="model.tipo_impuesto"
          class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 transition-all duration-200 hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
        >
          <option :value="null">Sin impuesto</option>
          <option value="porcentaje">Porcentaje</option>
          <option value="fijo">Fijo</option>
          <option value="n/a">N/A</option>
        </select>
      </div>

      <div class="space-y-2" v-if="model.tipo_impuesto === 'porcentaje' || model.tipo_impuesto === 'fijo'">
        <label class="text-sm font-semibold text-gray-700">Valor impuesto</label>
        <input
          v-model.number="model.valor_impuesto"
          type="number"
          min="0"
          step="0.01"
          inputmode="decimal"
          class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 transition-all duration-200 hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
        />
      </div>
    </div>

    <!-- Notas -->
    <div class="space-y-2">
      <label class="text-sm font-semibold text-gray-700">Notas</label>
      <textarea
        v-model.trim="model.notas"
        rows="3"
        class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 placeholder-gray-500 transition-all duration-200 hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
      ></textarea>
    </div>

    <!-- Imagen -->
    <div class="space-y-2">
      <label class="text-sm font-semibold text-gray-700">Imagen</label>
      <input
        type="file"
        accept="image/*"
        class="w-full rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 px-4 py-3 text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100 cursor-pointer transition"
        @change="onFile"
      />
      <p class="text-xs text-gray-500">Formatos: JPG, PNG, WEBP. Tamaño recomendado &lt; 2MB.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
const model = defineModel<any>({ required: true });

defineProps<{
  tipos: any[];
  categorias: any[];
  estados: any[];
  proveedores: any[];
  lotes: any[];
}>();

const emit = defineEmits<{ (e: 'imagen', file: File | null): void }>();

function onFile(e: Event) {
  const input = e.target as HTMLInputElement;
  emit('imagen', input.files?.[0] ?? null);
}
</script>
