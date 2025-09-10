<template>
  <!-- Layout con navegación (todas las rutas normales) -->
  <div v-if="showChrome" id="app" class="app">
    <Sidebar
      :is-open="sidebarOpen"
      @toggle="toggleSidebar"
      @close="closeSidebar"
    />
    <div class="main-content" :class="{ 'sidebar-open': sidebarOpen }">
      <Navbar
        @toggle-sidebar="toggleSidebar"
        :sidebar-open="sidebarOpen"
      />
      <main class="content">
        <router-view />
      </main>
    </div>
  </div>
 
  <!-- Layout limpio para login -->
  <div v-else class="auth-wrapper">
    <router-view />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import Sidebar from './components/Sidebar.vue'
import Navbar from './components/Navigation.vue'

const route = useRoute()
const showChrome = computed(() => !route.meta.hideChrome) // 👈 clave

const sidebarOpen = ref(true)

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}

const closeSidebar = () => {
  sidebarOpen.value = false
}

const handleResize = () => {
  sidebarOpen.value = window.innerWidth >= 768
}

onMounted(() => {
  handleResize()
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
})
</script>

<style>
/* Estilos globales */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  line-height: 1.6;
  color: #333;
}

#app {
  height: 100vh;
  overflow: hidden;
}
</style>

<style scoped>
.app {
  display: flex;
  height: 100vh;
  background: #f8fafc;
}

.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  transition: margin-left 0.3s ease;
  min-width: 0;
}

.content {
  flex: 1;
  padding: 24px;
  overflow-y: auto;
  background: #f8fafc;
}

@media (max-width: 768px) {
  .main-content { 
    margin-left: 0 !important; 
  }
  
  .content { 
    padding: 16px; 
  }
}

/* Contenedor limpio para login - SIN fondo gris */
.auth-wrapper {
  min-height: 100vh;
  width: 100vw;
  position: relative;
  /* Eliminamos el background para que el login maneje su propio fondo */
  padding: 0;
  margin: 0;
  overflow: hidden;
}
</style>