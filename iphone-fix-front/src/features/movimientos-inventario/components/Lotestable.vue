<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
      <div>
        <h2 class="text-lg font-semibold text-gray-900">Gestión de Lotes</h2>
        <p class="text-sm text-gray-500 mt-1">Próximamente: administra lotes y distribuye costos de flete</p>
      </div>
      <button
        @click="emit('nuevo-lote')"
        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Nuevo Lote
      </button>
    </div>

    <div class="p-12 text-center">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">En desarrollo</h3>
      <p class="mt-1 text-sm text-gray-500">Esta funcionalidad estará disponible próximamente.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
const emit = defineEmits<{
  'nuevo-lote': []
}>()
</script>