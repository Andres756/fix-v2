<template>
  <Teleport to="body">
    <transition
      name="modal"
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
      appear
    >
      <div v-if="open" class="fixed inset-0 z-[9999] overflow-y-auto">
        <!-- Backdrop -->
        <div
          class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
          @click="$emit('close')"
        ></div>

        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
          <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-gray-900">Nuevo Cliente</h3>
                  <p class="text-sm text-gray-500">Registra un nuevo cliente en el sistema</p>
                </div>
              </div>
              <button
                @click="$emit('close')"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Formulario -->
            <form @submit.prevent="guardar" class="p-6">
              <div class="space-y-5">
                <!-- Nombre -->
                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-gray-700">
                    Nombre completo *
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                    </div>
                    <input
                      v-model="form.nombre"
                      required
                      type="text"
                      placeholder="Juan Pérez González"
                      maxlength="100"
                      @input="validarNombre"
                      :class="[
                        'w-full pl-10 pr-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 transition-colors',
                        errors.nombre ? 'border-red-300 focus:border-red-500' : 'border-gray-300 focus:border-blue-500'
                      ]"
                    />
                  </div>
                  <p v-if="errors.nombre" class="text-sm text-red-600 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    {{ errors.nombre }}
                  </p>
                </div>

                <!-- Documento -->
                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-gray-700">
                    Número de documento *
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                      </svg>
                    </div>
                    <input
                      v-model="form.documento"
                      required
                      type="text"
                      placeholder="1234567890"
                      maxlength="20"
                      @input="validarDocumento"
                      :class="[
                        'w-full pl-10 pr-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 transition-colors',
                        errors.documento ? 'border-red-300 focus:border-red-500' : 'border-gray-300 focus:border-blue-500'
                      ]"
                    />
                  </div>
                  <p v-if="errors.documento" class="text-sm text-red-600 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    {{ errors.documento }}
                  </p>
                </div>

                <!-- Teléfono -->
                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-gray-700">
                    Teléfono *
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                      </svg>
                    </div>
                    <input
                      v-model="form.telefono"
                      required
                      type="tel"
                      placeholder="3001234567"
                      maxlength="15"
                      @input="validarTelefono"
                      :class="[
                        'w-full pl-10 pr-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 transition-colors',
                        errors.telefono ? 'border-red-300 focus:border-red-500' : 'border-gray-300 focus:border-blue-500'
                      ]"
                    />
                  </div>
                  <p v-if="errors.telefono" class="text-sm text-red-600 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    {{ errors.telefono }}
                  </p>
                </div>

                <!-- Correo -->
                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-gray-700">
                    Correo electrónico *
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                      </svg>
                    </div>
                    <input
                      v-model="form.correo"
                      required
                      type="email"
                      placeholder="juan.perez@email.com"
                      maxlength="100"
                      @input="validarCorreo"
                      :class="[
                        'w-full pl-10 pr-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 transition-colors',
                        errors.correo ? 'border-red-300 focus:border-red-500' : 'border-gray-300 focus:border-blue-500'
                      ]"
                    />
                  </div>
                  <p v-if="errors.correo" class="text-sm text-red-600 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    {{ errors.correo }}
                  </p>
                </div>

                <!-- Dirección -->
                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-gray-700">
                    Dirección *
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                    </div>
                    <input
                      v-model="form.direccion"
                      required
                      type="text"
                      placeholder="Calle 123 #45-67, Bogotá"
                      maxlength="200"
                      @input="validarDireccion"
                      :class="[
                        'w-full pl-10 pr-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 transition-colors',
                        errors.direccion ? 'border-red-300 focus:border-red-500' : 'border-gray-300 focus:border-blue-500'
                      ]"
                    />
                  </div>
                  <p v-if="errors.direccion" class="text-sm text-red-600 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    {{ errors.direccion }}
                  </p>
                </div>
              </div>

              <!-- Botones -->
              <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
                <button
                  type="button"
                  @click="$emit('close')"
                  class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  :disabled="!isFormValid || isSubmitting"
                  :class="[
                    'px-6 py-2.5 rounded-lg transition-colors font-medium flex items-center gap-2',
                    isFormValid && !isSubmitting
                      ? 'bg-blue-600 text-white hover:bg-blue-700'
                      : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                  ]"
                >
                  <svg v-if="!isSubmitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  <svg v-else class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ isSubmitting ? 'Guardando...' : 'Crear Cliente' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { createCliente, type CreateClientePayload } from '../api/clientes'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

defineProps<{ open: boolean }>()
const emit = defineEmits<{ (e: 'close'): void; (e: 'created', cliente: any): void }>()

const form = ref<CreateClientePayload>({
  nombre: '',
  documento: '',
  telefono: '',
  correo: '',
  direccion: '',
})

const errors = ref({
  nombre: '',
  documento: '',
  telefono: '',
  correo: '',
  direccion: '',
})

const isSubmitting = ref(false)

// Validaciones individuales
function validarNombre() {
  const valor = form.value.nombre.trim()
  
  if (!valor) {
    errors.value.nombre = ''
    return
  }
  
  if (valor.length < 3) {
    errors.value.nombre = 'El nombre debe tener al menos 3 caracteres'
    return
  }
  
  if (!/^[a-záéíóúñA-ZÁÉÍÓÚÑ\s]+$/.test(valor)) {
    errors.value.nombre = 'Solo se permiten letras y espacios'
    return
  }
  
  errors.value.nombre = ''
}

function validarDocumento() {
  const valor = form.value.documento.trim()
  
  if (!valor) {
    errors.value.documento = ''
    return
  }
  
  if (!/^[0-9]+$/.test(valor)) {
    errors.value.documento = 'Solo se permiten números'
    return
  }
  
  if (valor.length < 6) {
    errors.value.documento = 'El documento debe tener al menos 6 dígitos'
    return
  }
  
  errors.value.documento = ''
}

function validarTelefono() {
  const valor = (form.value.telefono ?? '').trim()

  if (!valor) {
    errors.value.telefono = ''
    return
  }
  
  // Eliminar espacios y guiones para validar
  const telefonoLimpio = valor.replace(/[\s-]/g, '')
  
  if (!/^[0-9]+$/.test(telefonoLimpio)) {
    errors.value.telefono = 'Solo se permiten números, espacios y guiones'
    return
  }
  
  if (telefonoLimpio.length < 7) {
    errors.value.telefono = 'El teléfono debe tener al menos 10 dígitos'
    return
  }
  
  if (telefonoLimpio.length > 15) {
    errors.value.telefono = 'El teléfono no puede tener más de 15 dígitos'
    return
  }
  
  errors.value.telefono = ''
}

function validarCorreo() {
  const valor = (form.value.correo ?? '').trim()
  
  if (!valor) {
    errors.value.correo = ''
    return
  }
  
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  
  if (!emailRegex.test(valor)) {
    errors.value.correo = 'Ingresa un correo electrónico válido'
    return
  }
  
  errors.value.correo = ''
}

function validarDireccion() {
  const valor = (form.value.direccion ?? '').trim()
  
  if (!valor) {
    errors.value.direccion = ''
    return
  }
  
  if (valor.length < 5) {
    errors.value.direccion = 'La dirección debe tener al menos 5 caracteres'
    return
  }
  
  errors.value.direccion = ''
}

// Computed para verificar si el formulario es válido
const isFormValid = computed(() => {
  const nombre = (form.value.nombre ?? '').trim()
  const documento = (form.value.documento ?? '').trim()
  const telefono = (form.value.telefono ?? '').trim()
  const correo = (form.value.correo ?? '').trim()
  const direccion = (form.value.direccion ?? '').trim()

  return (
    nombre.length >= 3 &&
    documento.length >= 6 &&
    telefono.length >= 10 &&
    correo.length > 0 &&
    direccion.length >= 5 &&
    !errors.value.nombre &&
    !errors.value.documento &&
    !errors.value.telefono &&
    !errors.value.correo &&
    !errors.value.direccion
  )
})

async function guardar() {
  // Validar todo antes de enviar
  validarNombre()
  validarDocumento()
  validarTelefono()
  validarCorreo()
  validarDireccion()
  
  if (!isFormValid.value) {
    toast.error('Por favor corrige los errores en el formulario')
    return
  }
  
  isSubmitting.value = true
  
  try {
    const nuevo = await createCliente(form.value)
    emit('created', nuevo)
    
    // Resetear formulario
    form.value = {
      nombre: '',
      documento: '',
      telefono: '',
      correo: '',
      direccion: '',
    }
    
    errors.value = {
      nombre: '',
      documento: '',
      telefono: '',
      correo: '',
      direccion: '',
    }
    
    toast.success('¡Cliente creado exitosamente! 🎉', { autoClose: 3000 })
  } catch (error: any) {
    const msg = error?.response?.data?.message || 'No se pudo crear el cliente'
    toast.error(msg, { autoClose: 3000 })
  } finally {
    isSubmitting.value = false
  }
}
</script>