<template>
  <div class="sidebar-wrapper">
    <div 
      v-if="isOpen && isMobile" 
      class="sidebar-overlay" 
      @click="$emit('close')"
    ></div>
    <aside 
      class="sidebar"
      :class="{ 'sidebar-open': isOpen, 'sidebar-mobile': isMobile }"
    >
      <!-- Logo/Header -->
      <div class="sidebar-header">
        <div class="logo-container">
          <div class="logo-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
              <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
              <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="logo-text">
            <h2>Tienda Apple</h2>
            <span>Sistema de Gestión</span>
          </div>
        </div>
      </div>

      <!-- Navigation Menu -->
      <nav class="sidebar-nav">
        <ul class="nav-list">
          <li class="nav-item" :class="{ active: activeRoute === 'dashboard' }">
            <router-link to="/" class="nav-link nav-link-dashboard" @click="handleNavClick">
              <div class="nav-icon blue-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <rect x="3" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                  <rect x="14" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                  <rect x="14" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                  <rect x="3" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                </svg>
              </div>
              <span class="nav-text">Dashboard</span>
              <div class="nav-indicator" v-if="activeRoute === 'dashboard'"></div>
            </router-link>
          </li>

          <li class="nav-item" :class="{ active: activeRoute === 'inventario' }">
            <router-link to="/inventario" class="nav-link nav-link-inventario" @click="handleNavClick">
              <div class="nav-icon blue-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <path d="M21 16V8a2 2 0 0 0-1-1.73L12 2 4 6.27A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73L12 22l8-4.27A2 2 0 0 0 21 16z" stroke="currentColor" stroke-width="2"/>
                </svg>
              </div>
              <span class="nav-text">Inventario</span>
              <div class="nav-indicator" v-if="activeRoute === 'inventario'"></div>
            </router-link>
          </li>

          <li class="nav-item" :class="{ active: activeRoute === 'facturacion' }">
            <router-link to="/facturacion" class="nav-link nav-link-facturacion" @click="handleNavClick">
              <div class="nav-icon green-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="currentColor" stroke-width="2"/>
                  <polyline points="14,2 14,8 20,8" stroke="currentColor" stroke-width="2"/>
                  <line x1="16" y1="13" x2="8" y2="13" stroke="currentColor" stroke-width="2"/>
                  <line x1="16" y1="17" x2="8" y2="17" stroke="currentColor" stroke-width="2"/>
                </svg>
              </div>
              <span class="nav-text">Facturación</span>
              <div class="nav-indicator" v-if="activeRoute === 'facturacion'"></div>
            </router-link>
          </li>

          <li class="nav-item" :class="{ active: activeRoute === 'ordenes' }">
            <router-link to="/ordenes" class="nav-link nav-link-ordenes" @click="handleNavClick">
              <div class="nav-icon orange-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                  <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" stroke="currentColor" stroke-width="2"/>
                </svg>
              </div>
              <span class="nav-text">Órdenes de Servicio</span>
              <div class="nav-indicator" v-if="activeRoute === 'ordenes'"></div>
            </router-link>
          </li>

          <li class="nav-item" :class="{ active: activeRoute === 'tecnicos' }">
            <router-link to="/ordenes/tecnicos" class="nav-link nav-link-tecnicos" @click="handleNavClick">
              <div class="nav-icon teal-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                  <path d="M5.5 21a6.5 6.5 0 0 1 13 0" stroke="currentColor" stroke-width="2"/>
                </svg>
              </div>
              <span class="nav-text">Técnicos</span>
              <div class="nav-indicator" v-if="activeRoute === 'tecnicos'"></div>
            </router-link>
          </li>

          <li class="nav-item" :class="{ active: activeRoute === 'gastos' }">
            <router-link to="/gastos" class="nav-link nav-link-gastos" @click="handleNavClick">
              <div class="nav-icon red-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <rect x="2" y="3" width="20" height="14" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                  <line x1="8" y1="21" x2="16" y2="21" stroke="currentColor" stroke-width="2"/>
                  <line x1="12" y1="17" x2="12" y2="21" stroke="currentColor" stroke-width="2"/>
                </svg>
              </div>
              <span class="nav-text">Gastos</span>
              <div class="nav-indicator" v-if="activeRoute === 'gastos'"></div>
            </router-link>
          </li>

          <li class="nav-item" :class="{ active: activeRoute === 'informes' }">
            <router-link to="/informes" class="nav-link nav-link-informes" @click="handleNavClick">
              <div class="nav-icon purple-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <line x1="18" y1="20" x2="18" y2="10" stroke="currentColor" stroke-width="2"/>
                  <line x1="12" y1="20" x2="12" y2="4" stroke="currentColor" stroke-width="2"/>
                  <line x1="6" y1="20" x2="6" y2="14" stroke="currentColor" stroke-width="2"/>
                </svg>
              </div>
              <span class="nav-text">Informes</span>
              <div class="nav-indicator" v-if="activeRoute === 'informes'"></div>
            </router-link>
          </li>
        </ul>

        <!-- Admin Section -->
        <div class="nav-section">
          <div class="section-header">
            <span>ADMINISTRADOR</span>
          </div>
          <ul class="nav-list">
            <li class="nav-item" :class="{ active: activeRoute === 'parametrizacion' }">
              <router-link to="/parametrizacion" class="nav-link nav-link-parametrizacion" @click="handleNavClick">
                <div class="nav-icon cyan-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" stroke="currentColor" stroke-width="2"/>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" stroke="currentColor" stroke-width="2"/>
                  </svg>
                </div>
                <span class="nav-text">Parametrización</span>
                <div class="nav-indicator" v-if="activeRoute === 'parametrizacion'"></div>
              </router-link>
            </li>

            <li class="nav-item" :class="{ active: activeRoute === 'usuarios' }">
              <router-link to="/usuarios" class="nav-link nav-link-usuarios" @click="handleNavClick">
                <div class="nav-icon purple-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2"/>
                    <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                  </svg>
                </div>
                <span class="nav-text">Usuarios</span>
                <div class="nav-indicator" v-if="activeRoute === 'usuarios'"></div>
              </router-link>
            </li>
          </ul>
        </div>
      </nav>

      <!-- Footer -->
      <div class="sidebar-footer">
        <div class="status-indicator">
          <div class="status-dot"></div>
          <span>Sistema Activo</span>
        </div>
      </div>
    </aside>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'

interface Props {
  isOpen: boolean
}

defineProps<Props>()
defineEmits<{
  toggle: []
  close: []
}>()

const route = useRoute()
const isMobile = ref(false)

const activeRoute = computed(() => {
  const path = route.path
  if (path === '/') return 'dashboard'
  return path.slice(1)
})

const handleNavClick = () => {
  if (isMobile.value) {
    // Emit close event for mobile
  }
}

const checkMobile = () => {
  isMobile.value = window.innerWidth < 768
}

onMounted(() => {
  checkMobile()
  window.addEventListener('resize', checkMobile)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile)
})
</script>

<style scoped>
.sidebar-wrapper {
  position: relative;
}

.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 998;
}

.sidebar {
  position: fixed;
  left: -280px;
  top: 0;
  width: 280px;
  height: 100vh;
  background: #2d3748;
  color: white;
  transition: left 0.3s ease;
  z-index: 999;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}

.sidebar-open {
  left: 0;
}

.sidebar-mobile {
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
}

@media (min-width: 768px) {
  .sidebar {
    position: relative;
    left: 0;
    box-shadow: none;
  }
  
  .sidebar-overlay {
    display: none;
  }
}

.sidebar-header {
  padding: 24px 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.logo-container {
  display: flex;
  align-items: center;
  gap: 12px;
}

.logo-icon {
  width: 40px;
  height: 40px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.logo-text h2 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: white;
}

.logo-text span {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.8);
}

.sidebar-nav {
  flex: 1;
  padding: 20px 0;
}

.nav-list {
  list-style: none;
  margin: 0;
  padding: 0;
}

.nav-item {
  margin: 0;
  position: relative;
}

.nav-link {
  display: flex;
  align-items: center;
  padding: 12px 20px;
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  transition: all 0.3s ease;
  border-left: 3px solid transparent;
  position: relative;
}

/* Efectos hover específicos por color */
.nav-link-dashboard:hover {
  background: rgba(96, 165, 250, 0.1);
  border-left-color: #60a5fa;
}

.nav-link-inventario:hover {
  background: rgba(96, 165, 250, 0.1);
  border-left-color: #60a5fa;
}

.nav-link-facturacion:hover {
  background: rgba(52, 211, 153, 0.1);
  border-left-color: #34d399;
}

.nav-link-ordenes:hover {
  background: rgba(251, 191, 36, 0.1);
  border-left-color: #fbbf24;
}

.nav-link-gastos:hover {
  background: rgba(248, 113, 113, 0.1);
  border-left-color: #f87171;
}

.nav-link-informes:hover {
  background: rgba(167, 139, 250, 0.1);
  border-left-color: #a78bfa;
}

.nav-link-parametrizacion:hover {
  background: rgba(34, 211, 238, 0.1);
  border-left-color: #22d3ee;
}

.nav-link-usuarios:hover {
  background: rgba(167, 139, 250, 0.1);
  border-left-color: #a78bfa;
}

.nav-link:hover {
  color: white;
}

.nav-item.active .nav-link {
  background: rgba(255, 255, 255, 0.15);
  color: white;
  border-left-color: #fff;
}

.nav-icon {
  width: 20px;
  height: 20px;
  margin-right: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Colored icons */
.blue-icon {
  color: #60a5fa;
}

.green-icon {
  color: #34d399;
}

.orange-icon {
  color: #fbbf24;
}

.red-icon {
  color: #f87171;
}

.purple-icon {
  color: #a78bfa;
}

.cyan-icon {
  color: #22d3ee;
}

.nav-text {
  font-size: 14px;
  font-weight: 500;
  flex: 1;
}

/* Indicador de elemento activo (puntico azul) */
.nav-indicator {
  width: 6px;
  height: 6px;
  background: #60a5fa;
  border-radius: 50%;
  margin-left: 8px;
  animation: pulse-indicator 2s infinite;
}

@keyframes pulse-indicator {
  0% {
    box-shadow: 0 0 0 0 rgba(96, 165, 250, 0.7);
  }
  70% {
    box-shadow: 0 0 0 4px rgba(96, 165, 250, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(96, 165, 250, 0);
  }
}

.nav-section {
  margin-top: 30px;
  padding-top: 20px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.section-header {
  padding: 0 20px 12px;
}

.section-header span {
  font-size: 11px;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.6);
  text-transform: uppercase;
  letter-spacing: 1px;
}

.sidebar-footer {
  padding: 20px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.status-indicator {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  color: rgba(255, 255, 255, 0.8);
}

.status-dot {
  width: 8px;
  height: 8px;
  background: #22c55e;
  border-radius: 50%;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(34, 197, 94, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
  }
}

/* Scrollbar styles */
.sidebar::-webkit-scrollbar {
  width: 4px;
}

.sidebar::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
}

.sidebar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.3);
  border-radius: 2px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.4);
}
</style>