<template>
  <span class="inline-flex items-center rounded-full font-medium" :class="classes">
    <span class="w-2 h-2 rounded-full mr-1.5" :class="dotClasses"></span>
    {{ formattedEstado }}
  </span>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  estado: string
  size?: 'sm' | 'md'
}>()

const formattedEstado = computed(() => {
  const estados = {
    'pendiente': 'Pendiente',
    'en_proceso': 'En Proceso',
    'completada': 'Completada',
    'completado': 'Completado',
    'cancelada': 'Cancelada',
    'cancelado': 'Cancelado'
  }
  return estados[props.estado as keyof typeof estados] || props.estado
})

const classes = computed(() => {
  const sizeClasses = {
    sm: 'px-2 py-0.5 text-xs',
    md: 'px-2.5 py-1 text-sm'
  }
  
  const colorClasses = {
    'pendiente': 'bg-red-50 text-red-700 border border-red-200',
    'en_proceso': 'bg-yellow-50 text-yellow-700 border border-yellow-200',
    'completada': 'bg-green-50 text-green-700 border border-green-200',
    'completado': 'bg-green-50 text-green-700 border border-green-200',
    'cancelada': 'bg-gray-50 text-gray-700 border border-gray-200',
    'cancelado': 'bg-gray-50 text-gray-700 border border-gray-200'
  }
  
  const size = props.size || 'md'
  const color = colorClasses[props.estado as keyof typeof colorClasses] || 'bg-gray-50 text-gray-700 border border-gray-200'
  
  return `${sizeClasses[size]} ${color}`
})

const dotClasses = computed(() => {
  const colors = {
    'pendiente': 'bg-red-400',
    'en_proceso': 'bg-yellow-400',
    'completada': 'bg-green-400',
    'completado': 'bg-green-400',
    'cancelada': 'bg-gray-400',
    'cancelado': 'bg-gray-400'
  }
  
  return colors[props.estado as keyof typeof colors] || 'bg-gray-400'
})
</script>