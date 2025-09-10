<template>
  <div class="space-y-6">
    <h4 class="flex items-center text-lg font-semibold text-gray-900">
      <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
      </svg>
      Detalles del repuesto
    </h4>
    
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
      <!-- Modelo compatible - Campo requerido -->
      <div class="space-y-2">
        <label class="flex items-center text-sm font-semibold text-gray-700">
          <span>Modelo compatible</span>
          <span class="text-red-500 ml-1">*</span>
          <svg v-if="detalle.modelo_compatible?.trim()" class="w-4 h-4 ml-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
        </label>
        <div class="relative">
          <input 
            v-model.trim="detalle.modelo_compatible" 
            type="text" 
            :class="[
              'w-full rounded-lg border-2 bg-white px-4 py-3 text-gray-900 placeholder-gray-500 transition-all duration-200',
              detalle.modelo_compatible?.trim() 
                ? 'border-green-300 focus:border-green-500 focus:ring-green-500/10' 
                : 'border-gray-200 hover:border-gray-300 focus:border-blue-500 focus:ring-blue-500/10',
              'focus:outline-none focus:ring-4'
            ]"
            placeholder="ej: iPhone 14 Pro, Samsung Galaxy S23"
          />
          <div v-if="detalle.modelo_compatible?.trim()" class="absolute inset-y-0 right-0 flex items-center px-4">
            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
        </div>
        <p v-if="!detalle.modelo_compatible?.trim()" class="text-xs text-red-600">Este campo es requerido</p>
      </div>

      <!-- Tipo de repuesto - Campo requerido -->
      <div class="space-y-2">
        <label class="flex items-center text-sm font-semibold text-gray-700">
          <span>Tipo de repuesto</span>
          <span class="text-red-500 ml-1">*</span>
          <svg v-if="detalle.tipo_repuesto?.trim()" class="w-4 h-4 ml-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
        </label>
        <div class="relative">
          <input 
            v-model.trim="detalle.tipo_repuesto" 
            type="text" 
            :class="[
              'w-full rounded-lg border-2 bg-white px-4 py-3 text-gray-900 placeholder-gray-500 transition-all duration-200',
              detalle.tipo_repuesto?.trim() 
                ? 'border-green-300 focus:border-green-500 focus:ring-green-500/10' 
                : 'border-gray-200 hover:border-gray-300 focus:border-blue-500 focus:ring-blue-500/10',
              'focus:outline-none focus:ring-4'
            ]"
            placeholder="ej: Pantalla, Batería, Cámara"
          />
          <div v-if="detalle.tipo_repuesto?.trim()" class="absolute inset-y-0 right-0 flex items-center px-4">
            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
        </div>
        <p v-if="!detalle.tipo_repuesto?.trim()" class="text-xs text-red-600">Este campo es requerido</p>
      </div>

      <!-- Referencia fabricante - Campo requerido -->
      <div class="space-y-2">
        <label class="flex items-center text-sm font-semibold text-gray-700">
          <span>Referencia fabricante</span>
          <span class="text-red-500 ml-1">*</span>
          <svg v-if="detalle.referencia_fabricante?.trim()" class="w-4 h-4 ml-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
        </label>
        <div class="relative">
          <input 
            v-model.trim="detalle.referencia_fabricante" 
            type="text" 
            :class="[
              'w-full rounded-lg border-2 bg-white px-4 py-3 text-gray-900 placeholder-gray-500 transition-all duration-200',
              detalle.referencia_fabricante?.trim() 
                ? 'border-green-300 focus:border-green-500 focus:ring-green-500/10' 
                : 'border-gray-200 hover:border-gray-300 focus:border-blue-500 focus:ring-blue-500/10',
              'focus:outline-none focus:ring-4'
            ]"
            placeholder="ej: A2633, SM-G996U"
          />
          <div v-if="detalle.referencia_fabricante?.trim()" class="absolute inset-y-0 right-0 flex items-center px-4">
            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
        </div>
        <p v-if="!detalle.referencia_fabricante?.trim()" class="text-xs text-red-600">Este campo es requerido</p>
      </div>

      <!-- Garantía - Campo requerido -->
      <div class="space-y-2">
        <label class="flex items-center text-sm font-semibold text-gray-700">
          <span>Garantía (meses)</span>
          <span class="text-red-500 ml-1">*</span>
          <svg v-if="detalle.garantia_meses && detalle.garantia_meses > 0" class="w-4 h-4 ml-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
        </label>
        <div class="relative">
          <input 
            v-model.number="detalle.garantia_meses" 
            type="number" 
            min="1" 
            max="60"
            :class="[
              'w-full rounded-lg border-2 bg-white px-4 py-3 text-gray-900 placeholder-gray-500 transition-all duration-200',
              detalle.garantia_meses && detalle.garantia_meses > 0 
                ? 'border-green-300 focus:border-green-500 focus:ring-green-500/10' 
                : 'border-gray-200 hover:border-gray-300 focus:border-blue-500 focus:ring-blue-500/10',
              'focus:outline-none focus:ring-4'
            ]"
            placeholder="ej: 12"
          />
          <div v-if="detalle.garantia_meses && detalle.garantia_meses > 0" class="absolute inset-y-0 right-0 flex items-center px-4">
            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
        </div>
        <p v-if="!detalle.garantia_meses || detalle.garantia_meses <= 0" class="text-xs text-red-600">Este campo es requerido (mínimo 1 mes)</p>
      </div>
    </div>

    <!-- Información adicional -->
    <div class="bg-amber-50 rounded-lg p-4 border border-amber-200">
      <div class="flex items-start space-x-3">
        <svg class="w-5 h-5 text-amber-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
          <h5 class="text-sm font-semibold text-amber-900">Información importante</h5>
          <p class="text-sm text-amber-800 mt-1">
            • Todos los campos son obligatorios para repuestos<br>
            • La referencia del fabricante debe ser exacta<br>
            • La garantía debe especificarse en meses (mínimo 1)
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
type DetalleRepuesto = { 
  modelo_compatible?: string; 
  tipo_repuesto?: string; 
  referencia_fabricante?: string; 
  garantia_meses?: number | null; 
};

const detalle = defineModel<DetalleRepuesto>('detalle', { required: true });
</script>