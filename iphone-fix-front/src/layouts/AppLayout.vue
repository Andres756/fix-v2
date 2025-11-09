<!-- src/layouts/AppLayout.vue -->
<template>
  <div class="flex min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <Sidebar :isOpen="sidebarOpen" @onToggle="toggleSidebar" />

    <!-- Contenido principal -->
    <div class="flex-1 flex flex-col">
      <Navigation :sidebarOpen="sidebarOpen" @onToggleSidebar="toggleSidebar" />
      <main class="p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import Sidebar from '../components/Sidebar.vue'
import Navigation from '../components/Navigation.vue'

// Estado del sidebar
const sidebarOpen = ref(false)

function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value
}
</script>
