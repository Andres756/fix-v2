<template>
  <span 
    :class="[
      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border',
      badgeClasses
    ]"
  >
    <span class="w-1.5 h-1.5 rounded-full mr-1.5" :class="dotClasses"></span>
    {{ estadoNombre }}
  </span>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  estadoCodigo: string
  estadoNombre: string
}>()

/**
 * 🎨 Clases del fondo y texto del badge
 */
const badgeClasses = computed(() => {
  const codigo = props.estadoCodigo?.toUpperCase()

  switch (codigo) {
    case 'ACT': // Activo
      return 'bg-yellow-100 text-yellow-800 border-yellow-200'

    case 'ASE': // Asegurado
      return 'bg-blue-100 text-blue-800 border-blue-200'

    case 'REA': // Reasignación
      return 'bg-orange-100 text-orange-800 border-orange-200'

    case 'CER': // Cerrado
      return 'bg-green-100 text-green-800 border-green-200'

    case 'CAN': // Cancelado
      return 'bg-gray-100 text-gray-800 border-gray-200'

    case 'DEV': // Devuelto
      return 'bg-purple-100 text-purple-800 border-purple-200'

    default:
      return 'bg-gray-100 text-gray-800 border-gray-200'
  }
})

/**
 * 🔘 Color del punto indicador
 */
const dotClasses = computed(() => {
  const codigo = props.estadoCodigo?.toUpperCase()

  switch (codigo) {
    case 'ACT': return 'bg-yellow-500'
    case 'ASE': return 'bg-blue-500'
    case 'REA': return 'bg-orange-500'
    case 'CER': return 'bg-green-500'
    case 'CAN': return 'bg-gray-500'
    case 'DEV': return 'bg-purple-500'
    default: return 'bg-gray-400'
  }
})
</script>
