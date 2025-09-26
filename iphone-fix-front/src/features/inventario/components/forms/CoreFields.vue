<template>
  <div class="space-y-8">
    <!-- Información básica -->
    <div>
      <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="space-y-2">
          <label class="flex items-center text-sm font-semibold text-gray-700">
            <span>Nombre</span>
            <span class="text-red-500 ml-1">*</span>
          </label>
          <input 
            v-model.trim="model.nombre" 
            type="text" 
            class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 
                   placeholder-gray-500 transition-all duration-200 
                   hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10" 
            placeholder="Ingresa el nombre del producto"
          />
        </div>

        <div class="space-y-2">
          <label class="flex items-center text-sm font-semibold text-gray-700">
            <span>Nombre detallado</span>
          </label>
          <input 
            v-model.trim="model.nombre_detallado" 
            type="text" 
            class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 
                   placeholder-gray-500 transition-all duration-200 
                   hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10" 
            placeholder="Ej: iPhone 13 Pro Max 256GB Sierra Blue"
          />
        </div>

        <div class="space-y-2">
          <label class="flex items-center text-sm font-semibold text-gray-700">
            <span>Código</span>
            <span class="text-red-500 ml-1">*</span>
          </label>
          <input 
            v-model.trim="model.codigo" 
            type="text" 
            class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 
                   placeholder-gray-500 transition-all duration-200 
                   hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10" 
            placeholder="Código único del producto"
          />
        </div>

        <div class="space-y-2">
          <label class="flex items-center text-sm font-semibold text-gray-700">
            <span>Tipo de inventario</span>
            <span class="text-red-500 ml-1">*</span>
          </label>
          <select 
            v-model.number="model.tipo_inventario_id" 
            class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 
                   transition-all duration-200 appearance-none cursor-pointer
                   hover:border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/10"
          >
            <option :value="null" disabled>Selecciona el tipo...</option>
            <option v-for="t in tipos" :key="t.id ?? t.value" :value="t.id ?? t.value">
              {{ t.nombre ?? t.label }}
            </option>
          </select>
        </div>

        <div class="space-y-2">
          <label class="flex items-center text-sm font-semibold text-gray-700">
            <span>Categoría</span>
            <span class="text-red-500 ml-1">*</span>
          </label>
          <select 
            v-model.number="model.categoria_id" 
            class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 
                   transition-all duration-200 appearance-none cursor-pointer
                   hover:border-gray-300 focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/10"
          >
            <option :value="null" disabled>Selecciona la categoría...</option>
            <option v-for="c in categorias" :key="c.id ?? c.value" :value="c.id ?? c.value">
              {{ c.nombre ?? c.label }}
            </option>
          </select>
        </div>

        <div class="space-y-2">
          <label class="flex items-center text-sm font-semibold text-gray-700">
            <span>Stock mínimo</span>
            <span class="text-red-500 ml-1">*</span>
          </label>
          <div class="relative">
            <input 
              v-model.number="model.stock_minimo" 
              type="number" 
              min="1" 
              class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 
                    placeholder-gray-500 transition-all duration-200 
                    hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10" 
              placeholder="5"
            />
            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
              <span class="text-sm text-gray-400">unidades</span>
            </div>
          </div>
        </div>

        <div class="space-y-2">
          <label class="text-sm font-semibold text-gray-700">Imagen</label>
          <input 
            type="file" 
            accept="image/*" 
            class="w-full rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 px-4 py-3 text-gray-700 
                   transition-all duration-200 cursor-pointer
                   hover:border-gray-400 hover:bg-gray-100 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10
                   file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium
                   file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" 
            @change="onFile" 
          />
        </div>
      </div>
    </div>

    <!-- Precios e impuestos -->
    <div>
      <div class="flex items-center space-x-2 mb-6">
        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h5 class="text-md font-semibold text-gray-900">Precios e impuestos</h5>
      </div>
      
      <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
        <div class="space-y-2">
          <label class="flex items-center text-sm font-semibold text-gray-700">
            <span>Precio de venta</span>
            <span class="text-red-500 ml-1">*</span>
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center px-4 pointer-events-none">
              <span class="text-sm text-gray-500">$</span>
            </div>
            <input 
              v-model.number="model.precio" 
              type="number" 
              min="1" 
              step="0.01"
              class="w-full rounded-lg border-2 border-gray-200 bg-white pl-10 pr-4 py-3 text-gray-900 
                     placeholder-gray-500 transition-all duration-200 
                     hover:border-gray-300 focus:border-green-500 focus:outline-none focus:ring-4 focus:ring-green-500/10" 
              placeholder="0.00"
            />
          </div>
        </div>

        <div class="space-y-2">
          <label class="flex items-center text-sm font-semibold text-gray-700">
            <span>Precio al mayor</span>
            <span class="text-red-500 ml-1">*</span>
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center px-4 pointer-events-none">
              <span class="text-sm text-gray-500">$</span>
            </div>
            <input 
              v-model.number="model.costo_mayor" 
              type="number" 
              min="1" 
              step="0.01"
              class="w-full rounded-lg border-2 border-gray-200 bg-white pl-10 pr-4 py-3 text-gray-900 
                     placeholder-gray-500 transition-all duration-200 
                     hover:border-gray-300 focus:border-green-500 focus:outline-none focus:ring-4 focus:ring-green-500/10" 
              placeholder="0.00"
            />
          </div>
        </div>

        <div class="space-y-2">
          <label class="text-sm font-semibold text-gray-700">Tipo de impuesto</label>
          <select 
            v-model="model.tipo_impuesto" 
            class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 
                   transition-all duration-200 appearance-none cursor-pointer
                   hover:border-gray-300 focus:border-violet-500 focus:outline-none focus:ring-4 focus:ring-violet-500/10"
          >
            <option value="n/a">Sin impuesto</option>
            <option value="porcentaje">Porcentaje (%)</option>
            <option value="fijo">Valor fijo ($)</option>
          </select>
        </div>

        <div class="space-y-2">
          <label class="text-sm font-semibold text-gray-700">Valor del impuesto</label>
          <div class="relative">
            <div v-if="model.tipo_impuesto === 'fijo'" class="absolute inset-y-0 left-0 flex items-center px-4 pointer-events-none">
              <span class="text-sm text-gray-500">$</span>
            </div>
            <input 
              v-model.number="model.valor_impuesto" 
              type="number" 
              min="0" 
              :step="model.tipo_impuesto === 'porcentaje' ? '0.01' : '1'"
              :disabled="model.tipo_impuesto === 'n/a'"
              :class="[
                'w-full rounded-lg border-2 bg-white py-3 text-gray-900 placeholder-gray-500 transition-all duration-200',
                model.tipo_impuesto === 'fijo' ? 'pl-10 pr-4' : 'px-4',
                model.tipo_impuesto === 'n/a' 
                  ? 'border-gray-200 bg-gray-100 cursor-not-allowed opacity-50' 
                  : 'border-gray-200 hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10'
              ]"
              placeholder="0"
            />
            <div v-if="model.tipo_impuesto === 'porcentaje'" class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
              <span class="text-sm text-gray-500">%</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Notas -->
    <div>
      <div class="flex items-center space-x-2 mb-4">
        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        <label class="text-sm font-semibold text-gray-700">Notas adicionales</label>
      </div>
      <textarea 
        v-model.trim="model.notas" 
        rows="2" 
        class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 
               placeholder-gray-500 transition-all duration-200 resize-none
               hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
        placeholder="Observaciones adicionales (opcional)"></textarea>
    </div>
  </div>
</template>

<script setup lang="ts">
const model = defineModel<any>({ required: true })

const props = defineProps<{
  tipos: any[]
  categorias: any[]
}>()

const emit = defineEmits<{ (e:'imagen', file: File | null): void }>()

function onFile(e: Event) {
  const input = e.target as HTMLInputElement
  emit('imagen', input.files?.[0] ?? null)
}
</script>