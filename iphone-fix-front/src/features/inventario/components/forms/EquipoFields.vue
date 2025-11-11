<template>
  <div class="space-y-6">
    <h4 class="flex items-center text-lg font-semibold text-gray-900">
      <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
      </svg>
      Detalles del equipo
    </h4>
    
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

      <!-- ✅ NUEVO: Selector de Modelo -->
      <div class="space-y-2">
        <label class="text-sm font-semibold text-gray-700">
          Modelo <span class="text-red-500">*</span>
        </label>
        <select 
          v-model="detalle.modelo_equipo_id" 
          required
          class="w-full rounded-lg border-2 border-gray-200 px-4 py-3"
        >
          <option value="">Seleccionar modelo...</option>
          <option 
            v-for="modelo in modelos" 
            :key="modelo.id" 
            :value="modelo.id"
          >
            {{ modelo.marca }} {{ modelo.nombre }}
            <span v-if="modelo.familia" class="text-gray-500">({{ modelo.familia }})</span>
          </option>
        </select>
      </div>

      <!-- IMEI 1 - Campo requerido -->
      <div class="space-y-2">
        <label class="flex items-center text-sm font-semibold text-gray-700">
          <span>IMEI 1</span>
          <span class="text-red-500 ml-1">*</span>
          <svg v-if="detalle.imei_1.trim()" class="w-4 h-4 ml-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
        </label>
        <div class="relative">
          <input 
            v-model.trim="detalle.imei_1" 
            type="text" 
            maxlength="15"
            :class="[
              'w-full rounded-lg border-2 bg-white px-4 py-3 text-gray-900 placeholder-gray-500 transition-all duration-200',
              detalle.imei_1.trim() 
                ? 'border-green-300 focus:border-green-500 focus:ring-green-500/10' 
                : 'border-gray-200 hover:border-gray-300 focus:border-blue-500 focus:ring-blue-500/10',
              'focus:outline-none focus:ring-4'
            ]"
            placeholder="Ingresa el IMEI principal"
          />
          <div v-if="detalle.imei_1.trim()" class="absolute inset-y-0 right-0 flex items-center px-4">
            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
        </div>
        <p v-if="!detalle.imei_1.trim()" class="text-xs text-red-600">Este campo es requerido</p>
        <p v-else-if="detalle.imei_1.length < 15" class="text-xs text-yellow-600">IMEI debe tener 15 dígitos</p>
      </div>

      <!-- IMEI 2 - Campo opcional -->
      <div class="space-y-2">
        <label class="text-sm font-semibold text-gray-700">IMEI 2</label>
        <div class="relative">
          <input 
            v-model.trim="detalle.imei_2" 
            type="text" 
            maxlength="15"
            class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 
                   placeholder-gray-500 transition-all duration-200 
                   hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
            placeholder="IMEI secundario (opcional)"
          />
        </div>
      </div>

      <!-- Estado físico -->
      <div class="space-y-2">
        <label class="text-sm font-semibold text-gray-700">Estado físico</label>
        <div class="relative">
          <select 
            v-model="detalle.estado_fisico" 
            class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 
                   transition-all duration-200 appearance-none cursor-pointer
                   hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
          >
            <option value="">Seleccionar estado...</option>
            <option value="Excelente">Excelente</option>
            <option value="Muy bueno">Muy bueno</option>
            <option value="Bueno">Bueno</option>
            <option value="Regular">Regular</option>
            <option value="Defectuoso">Defectuoso</option>
          </select>
          <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </div>
        </div>
      </div>

      <!-- iOS -->
      <div class="space-y-2">
        <label class="text-sm font-semibold text-gray-700">Versión iOS</label>
        <div class="relative">
          <input 
            v-model.trim="detalle.version_ios" 
            type="text" 
            class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 
                   placeholder-gray-500 transition-all duration-200 
                   hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
            placeholder="ej: 17.2.1"
          />
        </div>
      </div>

      <!-- Almacenamiento -->
      <div class="space-y-2">
        <label class="text-sm font-semibold text-gray-700">Almacenamiento</label>
        <div class="relative">
          <select 
            v-model="detalle.almacenamiento" 
            class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 
                   transition-all duration-200 appearance-none cursor-pointer
                   hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
          >
            <option value="">Seleccionar capacidad...</option>
            <option value="64GB">64GB</option>
            <option value="128GB">128GB</option>
            <option value="256GB">256GB</option>
            <option value="512GB">512GB</option>
            <option value="1TB">1TB</option>
          </select>
          <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </div>
        </div>
      </div>

      <!-- Color -->
      <div class="space-y-2">
        <label class="text-sm font-semibold text-gray-700">Color</label>
        <div class="relative">
          <select 
            v-model="detalle.color" 
            class="w-full rounded-lg border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 
                   transition-all duration-200 appearance-none cursor-pointer
                   hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
          >
            <option value="">Seleccionar color...</option>
            <option value="Negro">Negro</option>
            <option value="Blanco">Blanco</option>
            <option value="Azul">Azul</option>
            <option value="Morado">Morado</option>
            <option value="Rosa">Rosa</option>
            <option value="Amarillo">Amarillo</option>
            <option value="Verde">Verde</option>
            <option value="Rojo">Rojo</option>
            <option value="Dorado">Dorado</option>
            <option value="Plateado">Plateado</option>
          </select>
          <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Información adicional -->
    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
      <div class="flex items-start space-x-3">
        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
          <h5 class="text-sm font-semibold text-blue-900">Información importante</h5>
          <p class="text-sm text-blue-800 mt-1">
            • El IMEI 1 es obligatorio para todos los equipos<br>
            • Verifica que los IMEIs sean válidos antes de guardar<br>
            • El estado físico ayuda a determinar el precio de venta
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { fetchModelosEquiposOptions } from '../../api/modelosEquipos'

type DetalleEquipo = {
  modelo_equipo_id: number | null  // ✅ NUEVO
  imei_1: string
  imei_2?: string
  estado_fisico?: string
  version_ios?: string
  almacenamiento?: string
  color?: string
}

const detalle = defineModel<DetalleEquipo>('detalle', { required: true })
const modelos = ref<any[]>([])

onMounted(async () => {
  modelos.value = await fetchModelosEquiposOptions()
})
</script>