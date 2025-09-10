<template>
  <div class="space-y-6">
    <h4 class="flex items-center text-lg font-semibold text-gray-900">
      <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
      </svg>
      Detalles del producto
    </h4>
    
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
      <!-- Material - Campo requerido -->
      <div class="space-y-2">
        <label class="flex items-center text-sm font-semibold text-gray-700">
          <span>Material</span>
          <span class="text-red-500 ml-1">*</span>
          <svg v-if="detalle.material?.trim()" class="w-4 h-4 ml-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
        </label>
        <div class="relative">
          <input 
            v-model.trim="detalle.material" 
            type="text" 
            :class="[
              'w-full rounded-lg border-2 bg-white px-4 py-3 text-gray-900 placeholder-gray-500 transition-all duration-200',
              detalle.material?.trim() 
                ? 'border-green-300 focus:border-green-500 focus:ring-green-500/10' 
                : 'border-gray-200 hover:border-gray-300 focus:border-blue-500 focus:ring-blue-500/10',
              'focus:outline-none focus:ring-4'
            ]"
            placeholder="ej: Plástico, Metal, Silicona"
          />
          <div v-if="detalle.material?.trim()" class="absolute inset-y-0 right-0 flex items-center px-4">
            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
        </div>
        <p v-if="!detalle.material?.trim()" class="text-xs text-red-600">Este campo es requerido</p>
      </div>

      <!-- Compatibilidad - Campo requerido -->
      <div class="space-y-2">
        <label class="flex items-center text-sm font-semibold text-gray-700">
          <span>Compatibilidad</span>
          <span class="text-red-500 ml-1">*</span>
          <svg v-if="detalle.compatibilidad?.trim()" class="w-4 h-4 ml-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
        </label>
        <div class="relative">
          <input 
            v-model.trim="detalle.compatibilidad" 
            type="text" 
            :class="[
              'w-full rounded-lg border-2 bg-white px-4 py-3 text-gray-900 placeholder-gray-500 transition-all duration-200',
              detalle.compatibilidad?.trim() 
                ? 'border-green-300 focus:border-green-500 focus:ring-green-500/10' 
                : 'border-gray-200 hover:border-gray-300 focus:border-blue-500 focus:ring-blue-500/10',
              'focus:outline-none focus:ring-4'
            ]"
            placeholder="ej: iPhone 14, Samsung Galaxy S23"
          />
          <div v-if="detalle.compatibilidad?.trim()" class="absolute inset-y-0 right-0 flex items-center px-4">
            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
        </div>
        <p v-if="!detalle.compatibilidad?.trim()" class="text-xs text-red-600">Este campo es requerido</p>
      </div>

      <!-- Tipo de accesorio - Campo requerido -->
      <div class="space-y-2">
        <label class="flex items-center text-sm font-semibold text-gray-700">
          <span>Tipo de accesorio</span>
          <span class="text-red-500 ml-1">*</span>
          <svg v-if="detalle.tipo_accesorio?.trim()" class="w-4 h-4 ml-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
        </label>
        <div class="relative">
          <input 
            v-model.trim="detalle.tipo_accesorio" 
            type="text" 
            :class="[
              'w-full rounded-lg border-2 bg-white px-4 py-3 text-gray-900 placeholder-gray-500 transition-all duration-200',
              detalle.tipo_accesorio?.trim() 
                ? 'border-green-300 focus:border-green-500 focus:ring-green-500/10' 
                : 'border-gray-200 hover:border-gray-300 focus:border-blue-500 focus:ring-blue-500/10',
              'focus:outline-none focus:ring-4'
            ]"
            placeholder="ej: Funda, Protector, Cargador"
          />
          <div v-if="detalle.tipo_accesorio?.trim()" class="absolute inset-y-0 right-0 flex items-center px-4">
            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
        </div>
        <p v-if="!detalle.tipo_accesorio?.trim()" class="text-xs text-red-600">Este campo es requerido</p>
      </div>
    </div>

    <!-- Información adicional -->
    <div class="bg-emerald-50 rounded-lg p-4 border border-emerald-200">
      <div class="flex items-start space-x-3">
        <svg class="w-5 h-5 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
          <h5 class="text-sm font-semibold text-emerald-900">Información importante</h5>
          <p class="text-sm text-emerald-800 mt-1">
            • Todos los campos son obligatorios para productos<br>
            • Especifica claramente la compatibilidad con dispositivos<br>
            • El material ayuda a categorizar el tipo de producto
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
type DetalleProducto = { 
  material?: string; 
  compatibilidad?: string; 
  tipo_accesorio?: string; 
};

const detalle = defineModel<DetalleProducto>('detalle', { required: true });
</script>