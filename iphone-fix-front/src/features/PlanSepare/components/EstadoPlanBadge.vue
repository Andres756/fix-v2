<template>
  <span 
    :class="[
      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
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

const badgeClasses = computed(() => {
  const codigo = props.estadoCodigo?.toUpperCase()
  
  switch (codigo) {
    case 'ACT':
    case 'ABIERTO':
      return 'bg-yellow-100 text-yellow-800'
    
    case 'ASE':
    case 'ASEGURADO':
      return 'bg-blue-100 text-blue-800'
    
    case 'CER':
    case 'FACTURADO':
    case 'FINALIZADO':
      return 'bg-green-100 text-green-800'
    
    case 'EXP':
    case 'EXPIRADO':
      return 'bg-red-100 text-red-800'
    
    case 'CAN':
    case 'CANCELADO':
      return 'bg-gray-100 text-gray-800'
    
    case 'DEV':
    case 'DEVUELTO':
      return 'bg-purple-100 text-purple-800'
    
    case 'REA':
    case 'PEN_REA':
    case 'REASIGNACION':
      return 'bg-orange-100 text-orange-800'
    
    default:
      return 'bg-gray-100 text-gray-800'
  }
})

const dotClasses = computed(() => {
  const codigo = props.estadoCodigo?.toUpperCase()
  
  switch (codigo) {
    case 'ACT':
    case 'ABIERTO':
      return 'bg-yellow-500'
    
    case 'ASE':
    case 'ASEGURADO':
      return 'bg-blue-500'
    
    case 'CER':
    case 'FACTURADO':
    case 'FINALIZADO':
      return 'bg-green-500'
    
    case 'EXP':
    case 'EXPIRADO':
      return 'bg-red-500'
    
    case 'CAN':
    case 'CANCELADO':
      return 'bg-gray-500'
    
    case 'DEV':
    case 'DEVUELTO':
      return 'bg-purple-500'
    
    case 'REA':
    case 'PEN_REA':
    case 'REASIGNACION':
      return 'bg-orange-500'
    
    default:
      return 'bg-gray-500'
  }
})
</script>