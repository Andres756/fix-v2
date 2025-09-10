<template>
  <div class="space-y-8">
    <!-- Información básica -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
      <div class="space-y-2">
        <label class="flex items-center text-sm font-semibold text-gray-700">
          <span>Nombre</span>
          <span class="text-red-500 ml-1">*</span>
        </label>
        <div class="relative">
          <input 
            v-model.trim="model.nombre" 
            type="text" 
            class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 
                   placeholder-gray-500 transition-all duration-200 
                   hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10" 
            placeholder="Ingresa el nombre del producto"
          />
        </div>
      </div>

      <!-- NUEVO: Nombre detallado -->
      <div class="space-y-2">
        <label class="flex items-center text-sm font-semibold text-gray-700">
          <span>Nombre detallado</span>
        </label>
        <div class="relative">
          <input 
            v-model.trim="model.nombre_detallado" 
            type="text" 
            class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 
                   placeholder-gray-500 transition-all duration-200 
                   hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10" 
            placeholder="Ej: iPhone 13 Pro Max 256GB Sierra Blue"
          />
        </div>
      </div>

      <div class="space-y-2">
        <label class="flex items-center text-sm font-semibold text-gray-700">
          <span>Código</span>
          <span class="text-red-500 ml-1">*</span>
        </label>
        <div class="relative">
          <input 
            v-model.trim="model.codigo" 
            type="text" 
            class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 
                   placeholder-gray-500 transition-all duration-200 
                   hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10" 
            placeholder="Código único del producto"
          />
        </div>
      </div>

      <div class="space-y-2">
        <label class="flex items-center text-sm font-semibold text-gray-700">
          <span>Tipo de inventario</span>
          <span class="text-red-500 ml-1">*</span>
        </label>
        <div class="relative group">
          <select 
            v-model.number="model.tipo_inventario_id" 
            class="w-full rounded-xl border-2 border-gray-300 bg-gradient-to-br from-white to-gray-50 px-4 py-3.5 text-gray-900 
                   transition-all duration-300 ease-out appearance-none cursor-pointer shadow-sm
                   hover:border-indigo-400 hover:shadow-md hover:from-indigo-50 hover:to-white
                   focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:shadow-lg
                   group-hover:transform group-hover:scale-[1.01]"
          >
            <option :value="null" disabled class="text-gray-400 bg-gray-50">✨ Selecciona el tipo...</option>
            <option v-for="t in tipos" :key="t.id ?? t.value" :value="t.id ?? t.value" 
                    class="text-gray-800 bg-white hover:bg-indigo-50 hover:text-indigo-700 py-3 px-4 font-medium">
              📦 {{ t.nombre ?? t.label }}
            </option>
          </select>
          <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none transition-transform duration-300 group-hover:scale-110">
            <div class="w-6 h-6 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
              <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <div class="space-y-2">
        <label class="flex items-center text-sm font-semibold text-gray-700">
          <span>Categoría</span>
          <span class="text-red-500 ml-1">*</span>
        </label>
        <div class="relative group">
          <select 
            v-model.number="model.categoria_id" 
            class="w-full rounded-xl border-2 border-gray-300 bg-gradient-to-br from-white to-gray-50 px-4 py-3.5 text-gray-900 
                   transition-all duration-300 ease-out appearance-none cursor-pointer shadow-sm
                   hover:border-emerald-400 hover:shadow-md hover:from-emerald-50 hover:to-white
                   focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:shadow-lg
                   group-hover:transform group-hover:scale-[1.01]"
          >
            <option :value="null" disabled class="text-gray-400 bg-gray-50">🏷️ Selecciona la categoría...</option>
            <option v-for="c in categorias" :key="c.id ?? c.value" :value="c.id ?? c.value" 
                    class="text-gray-800 bg-white hover:bg-emerald-50 hover:text-emerald-700 py-3 px-4 font-medium">
              📂 {{ c.nombre ?? c.label }}
            </option>
          </select>
          <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none transition-transform duration-300 group-hover:scale-110">
            <div class="w-6 h-6 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center">
              <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <div class="space-y-2">
        <label class="flex items-center text-sm font-semibold text-gray-700">
          <span>Proveedor</span>
          <span class="text-red-500 ml-1">*</span>
        </label>
        <div class="relative group">
          <select 
            v-model.number="model.proveedor_id" 
            class="w-full rounded-xl border-2 border-gray-300 bg-gradient-to-br from-white to-gray-50 px-4 py-3.5 text-gray-900 
                   transition-all duration-300 ease-out appearance-none cursor-pointer shadow-sm
                   hover:border-amber-400 hover:shadow-md hover:from-amber-50 hover:to-white
                   focus:border-amber-500 focus:outline-none focus:ring-4 focus:ring-amber-500/20 focus:shadow-lg
                   group-hover:transform group-hover:scale-[1.01]"
          >
            <option :value="null" disabled class="text-gray-400 bg-gray-50">🤝 Selecciona el proveedor...</option>
            <option v-for="p in proveedores" :key="p.id ?? p.value" :value="p.id ?? p.value" 
                    class="text-gray-800 bg-white hover:bg-amber-50 hover:text-amber-700 py-3 px-4 font-medium">
              🏢 {{ p.nombre ?? p.label }}
            </option>
          </select>
          <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none transition-transform duration-300 group-hover:scale-110">
            <div class="w-6 h-6 bg-gradient-to-br from-amber-500 to-orange-600 rounded-full flex items-center justify-center">
              <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <div class="space-y-2">
        <label class="flex items-center text-sm font-semibold text-gray-700">
          <span>Estado</span>
          <span class="text-red-500 ml-1">*</span>
        </label>
        <div class="relative group">
          <select 
            v-model.number="model.estado_inventario_id" 
            class="w-full rounded-xl border-2 border-gray-300 bg-gradient-to-br from-white to-gray-50 px-4 py-3.5 text-gray-900 
                   transition-all duration-300 ease-out appearance-none cursor-pointer shadow-sm
                   hover:border-rose-400 hover:shadow-md hover:from-rose-50 hover:to-white
                   focus:border-rose-500 focus:outline-none focus:ring-4 focus:ring-rose-500/20 focus:shadow-lg
                   group-hover:transform group-hover:scale-[1.01]"
          >
            <option :value="null" disabled class="text-gray-400 bg-gray-50">📊 Selecciona el estado...</option>
            <option v-for="e in estados" :key="e.id ?? e.value" :value="e.id ?? e.value" 
                    class="text-gray-800 bg-white hover:bg-rose-50 hover:text-rose-700 py-3 px-4 font-medium">
              🎯 {{ e.nombre ?? e.label }}
            </option>
          </select>
          <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none transition-transform duration-300 group-hover:scale-110">
            <div class="w-6 h-6 bg-gradient-to-br from-rose-500 to-pink-600 rounded-full flex items-center justify-center">
              <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Inventario y archivos -->
    <div>
      <div class="flex items-center space-x-2 mb-6">
        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
        <h5 class="text-md font-semibold text-gray-900">Control de inventario</h5>
      </div>
      
      <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
        <div class="space-y-2">
          <label class="text-sm font-semibold text-gray-700">Lote</label>
          <div class="relative group">
            <select 
              v-model.number="(model.lote_id as any)" 
              class="w-full rounded-xl border-2 border-gray-300 bg-gradient-to-br from-white to-gray-50 px-4 py-3.5 text-gray-900 
                     transition-all duration-300 ease-out appearance-none cursor-pointer shadow-sm
                     hover:border-cyan-400 hover:shadow-md hover:from-cyan-50 hover:to-white
                     focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-500/20 focus:shadow-lg
                     group-hover:transform group-hover:scale-[1.01]"
            >
              <option :value="null" class="text-gray-400 bg-gray-50">🏷️ Sin lote asignado</option>
              <option v-for="l in lotes" :key="l.id ?? l.value" :value="l.id ?? l.value" 
                      class="text-gray-800 bg-white hover:bg-cyan-50 hover:text-cyan-700 py-3 px-4 font-medium">
                📋 {{ l.nombre ?? l.label ?? l.codigo_lote }}
              </option>
            </select>
            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none transition-transform duration-300 group-hover:scale-110">
              <div class="w-6 h-6 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-full flex items-center justify-center">
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Stock inicial (oculto si es "Sin stock") -->
        <div class="space-y-2" v-if="!isSinStock">
          <label class="text-sm font-semibold text-gray-700">Stock inicial</label>
          <div class="relative">
            <input 
              v-model.number="(model.stock as any)" 
              type="number"
              min="0" 
              class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 
                    placeholder-gray-500 transition-all duration-200 
                    hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10" 
              placeholder="0"
            />
            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
              <span class="text-sm text-gray-400">unidades</span>
            </div>
          </div>
        </div>

        <!-- Stock mínimo (oculto si es "Sin stock") -->
        <div class="space-y-2" v-if="!isSinStock">
          <label class="text-sm font-semibold text-gray-700">Stock mínimo</label>
          <div class="relative">
            <input 
              v-model.number="(model.stock_minimo as any)" 
              type="number" 
              min="0" 
              class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 
                    placeholder-gray-500 transition-all duration-200 
                    hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10" 
              placeholder="0"
            />
            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
              <span class="text-sm text-gray-400">unidades</span>
            </div>
          </div>
        </div>


        <div class="space-y-2">
          <label class="text-sm font-semibold text-gray-700">Imagen</label>
          <div class="relative">
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
    </div>

    <!-- Precios e impuestos -->
    <div>
      <div class="flex items-center space-x-2 mb-6">
        <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
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
              v-model.number="(model.precio as any)" 
              type="number" 
              min="0" 
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
            <span>Costo de adquisición</span>
            <span class="text-red-500 ml-1">*</span>
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center px-4 pointer-events-none">
              <span class="text-sm text-gray-500">$</span>
            </div>
            <input 
              v-model.number="(model.costo as any)" 
              type="number" 
              min="0" 
              step="0.01"
              class="w-full rounded-lg border-2 border-gray-200 bg-white pl-10 pr-4 py-3 text-gray-900 
                     placeholder-gray-500 transition-all duration-200 
                     hover:border-gray-300 focus:border-orange-500 focus:outline-none focus:ring-4 focus:ring-orange-500/10" 
              placeholder="0.00"
            />
          </div>
        </div>

        <!-- NUEVO: Costo al por mayor -->
        <div class="space-y-2">
          <label class="flex items-center text-sm font-semibold text-gray-700">
            <span>Costo al por mayor</span>
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center px-4 pointer-events-none">
              <span class="text-sm text-gray-500">$</span>
            </div>
            <input 
              v-model.number="(model.costo_mayor as any)" 
              type="number" 
              min="0" 
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
          <div class="relative group">
            <select 
              v-model="(model.tipo_impuesto as any)" 
              class="w-full rounded-xl border-2 border-gray-300 bg-gradient-to-br from-white to-gray-50 px-4 py-3.5 text-gray-900 
                     transition-all duration-300 ease-out appearance-none cursor-pointer shadow-sm
                     hover:border-violet-400 hover:shadow-md hover:from-violet-50 hover:to-white
                     focus:border-violet-500 focus:outline-none focus:ring-4 focus:ring-violet-500/20 focus:shadow-lg
                     group-hover:transform group-hover:scale-[1.01]"
            >
              <option value="n/a" class="text-gray-800 bg-white hover:bg-violet-50 hover:text-violet-700 py-3 px-4 font-medium">🚫 Sin impuesto</option>
              <option value="porcentaje" class="text-gray-800 bg-white hover:bg-violet-50 hover:text-violet-700 py-3 px-4 font-medium">📊 Porcentaje (%)</option>
              <option value="fijo" class="text-gray-800 bg-white hover:bg-violet-50 hover:text-violet-700 py-3 px-4 font-medium">💰 Valor fijo ($)</option>
            </select>
            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none transition-transform duration-300 group-hover:scale-110">
              <div class="w-6 h-6 bg-gradient-to-br from-violet-500 to-purple-600 rounded-full flex items-center justify-center">
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <div class="space-y-2">
          <label class="text-sm font-semibold text-gray-700">Valor del impuesto</label>
          <div class="relative">
            <div v-if="model.tipo_impuesto === 'fijo'" class="absolute inset-y-0 left-0 flex items-center px-4 pointer-events-none">
              <span class="text-sm text-gray-500">$</span>
            </div>
            <input 
              v-model.number="(model.valor_impuesto as any)" 
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
    <div class="space-y-2">
      <label class="text-sm font-semibold text-gray-700">Notas adicionales</label>
      <textarea 
        v-model.trim="model.notas" 
        rows="4" 
        class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 
               placeholder-gray-500 transition-all duration-200 resize-none
               hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"></textarea>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, watchEffect } from 'vue'

const model = defineModel<any>({ required: true })

const props = defineProps<{
  tipos: any[]
  categorias: any[]
  estados: any[]
  proveedores: any[]
  lotes: any[]
}>()

const emit = defineEmits<{ (e:'imagen', file: File | null): void }>()
function onFile(e: Event) {
  const input = e.target as HTMLInputElement
  emit('imagen', input.files?.[0] ?? null)
}

// Detecta si el estado seleccionado es "SIN STOCK"
const isSinStock = computed(() => {
  const idSel = Number(model.value?.estado_inventario_id ?? 0)
  const match = (props.estados || []).find(e => Number(e.id ?? e.value) === idSel)
  const nombre = String(match?.nombre ?? match?.label ?? '').trim().toUpperCase()
  return nombre === 'SIN STOCK'
})

// Si es "SIN STOCK", fuerza valores a 0
watchEffect(() => {
  if (isSinStock.value && model.value) {
    model.value.stock = 0
    model.value.stock_minimo = 0
  }
})

// (opcional) tu sync de nombre_full → nombre_detallado
watchEffect(() => {
  const m = model as any
  if (!m?.value) return
  if (m.value.nombre_full && !m.value.nombre_detallado) {
    m.value.nombre_detallado = m.value.nombre_full
  }
})
</script>
