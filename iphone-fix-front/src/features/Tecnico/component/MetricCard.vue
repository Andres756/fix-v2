<template>
  <div class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm font-medium text-gray-600 mb-1">{{ title }}</p>
        <p class="text-2xl font-bold" :class="colorClasses.text">{{ formattedValue }}</p>
      </div>
      <div class="text-2xl" :class="colorClasses.bg">
        {{ icon }}
      </div>
    </div>
    
    <!-- Indicador visual opcional -->
    <div v-if="showIndicator" class="mt-4">
      <div class="w-full bg-gray-200 rounded-full h-2">
        <div 
          class="h-2 rounded-full transition-all duration-300"
          :class="colorClasses.bar"
          :style="{ width: `${percentage}%` }"
        ></div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  title: string
  value: number
  icon: string
  color: 'blue' | 'green' | 'yellow' | 'purple' | 'red'
  maxValue?: number
  showIndicator?: boolean
}>()

const formattedValue = computed(() => {
  return props.value.toLocaleString()
})

const percentage = computed(() => {
  if (!props.maxValue || props.maxValue === 0) return 0
  return Math.min((props.value / props.maxValue) * 100, 100)
})

const colorClasses = computed(() => {
  const colors = {
    blue: {
      text: 'text-blue-600',
      bg: 'text-blue-100 bg-blue-50 w-12 h-12 rounded-lg flex items-center justify-center',
      bar: 'bg-blue-500'
    },
    green: {
      text: 'text-green-600',
      bg: 'text-green-100 bg-green-50 w-12 h-12 rounded-lg flex items-center justify-center',
      bar: 'bg-green-500'
    },
    yellow: {
      text: 'text-yellow-600',
      bg: 'text-yellow-100 bg-yellow-50 w-12 h-12 rounded-lg flex items-center justify-center',
      bar: 'bg-yellow-500'
    },
    purple: {
      text: 'text-purple-600',
      bg: 'text-purple-100 bg-purple-50 w-12 h-12 rounded-lg flex items-center justify-center',
      bar: 'bg-purple-500'
    },
    red: {
      text: 'text-red-600',
      bg: 'text-red-100 bg-red-50 w-12 h-12 rounded-lg flex items-center justify-center',
      bar: 'bg-red-500'
    }
  }
  return colors[props.color]
})
</script>