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
          <!-- Dashboard - Azul -->
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
              <div class="nav-indicator blue-indicator" v-if="activeRoute === 'dashboard'"></div>
            </router-link>
          </li>

          <!-- Inventario - Indigo -->
          <li class="nav-item" :class="{ active: activeRoute === 'inventario' }">
            <router-link to="/inventario" class="nav-link nav-link-inventario" @click="handleNavClick">
              <div class="nav-icon indigo-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <path d="M21 16V8a2 2 0 0 0-1-1.73L12 2 4 6.27A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73L12 22l8-4.27A2 2 0 0 0 21 16z" stroke="currentColor" stroke-width="2"/>
                </svg>
              </div>
              <span class="nav-text">Inventario</span>
              <div class="nav-indicator indigo-indicator" v-if="activeRoute === 'inventario'"></div>
            </router-link>
          </li>

          <!-- Facturación - Verde -->
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
              <div class="nav-indicator green-indicator" v-if="activeRoute === 'facturacion'"></div>
            </router-link>
          </li>

          <!-- Plan Separe - Amarillo -->
          <li class="nav-item" :class="{ active: activeRoute === 'plan-separe' }">
            <router-link to="/plan-separe" class="nav-link nav-link-plansepare" @click="handleNavClick">
              <div class="nav-icon indigo-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke="currentColor" stroke-width="2"/>
                </svg>
              </div>
              <span class="nav-text">Plan Separe</span>
              <div class="nav-indicator" v-if="activeRoute === 'plan-separe'"></div>
            </router-link>
          </li>

          <!-- Órdenes de Servicio - Naranja -->
          <li class="nav-item" :class="{ active: activeRoute === 'ordenes' }">
            <router-link to="/ordenes" class="nav-link nav-link-ordenes" @click="handleNavClick">
              <div class="nav-icon orange-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                  <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" stroke="currentColor" stroke-width="2"/>
                </svg>
              </div>
              <span class="nav-text">Órdenes de Servicio</span>
              <div class="nav-indicator orange-indicator" v-if="activeRoute === 'ordenes'"></div>
            </router-link>
          </li>

          <!-- Técnicos - Teal/Turquesa -->
          <li class="nav-item" :class="{ active: activeRoute === 'tecnicos' }">
            <router-link to="/ordenes/tecnicos" class="nav-link nav-link-tecnicos" @click="handleNavClick">
              <div class="nav-icon teal-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                  <path d="M5.5 21a6.5 6.5 0 0 1 13 0" stroke="currentColor" stroke-width="2"/>
                </svg>
              </div>
              <span class="nav-text">Técnicos</span>
              <div class="nav-indicator teal-indicator" v-if="activeRoute === 'tecnicos'"></div>
            </router-link>
          </li>

          <!-- Gastos - Rojo -->
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
              <div class="nav-indicator red-indicator" v-if="activeRoute === 'gastos'"></div>
            </router-link>
          </li>

          <!-- Informes - Púrpura -->
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
              <div class="nav-indicator purple-indicator" v-if="activeRoute === 'informes'"></div>
            </router-link>
          </li>
        </ul>

        <!-- Admin Section -->
        <div class="nav-section">
          <div class="section-header">
            <span>ADMINISTRADOR</span>
          </div>
          <ul class="nav-list">
            <!-- Parametrización - Cyan -->
            <li class="nav-item" :class="{ active: activeRoute === 'parametrizacion' }">
              <router-link to="/parametrizacion" class="nav-link nav-link-parametrizacion" @click="handleNavClick">
                <div class="nav-icon cyan-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" stroke="currentColor" stroke-width="2"/>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" stroke="currentColor" stroke-width="2"/>
                  </svg>
                </div>
                <span class="nav-text">Parametrización</span>
                <div class="nav-indicator cyan-indicator" v-if="activeRoute === 'parametrizacion'"></div>
              </router-link>
            </li>

            <!-- Usuarios - Rosa -->
            <li class="nav-item" :class="{ active: activeRoute === 'usuarios' }">
              <router-link to="/usuarios" class="nav-link nav-link-usuarios" @click="handleNavClick">
                <div class="nav-icon pink-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2"/>
                    <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                  </svg>
                </div>
                <span class="nav-text">Usuarios</span>
                <div class="nav-indicator pink-indicator" v-if="activeRoute === 'usuarios'"></div>
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
  if (path.startsWith('/ordenes/tecnicos')) return 'tecnicos'
  if (path.startsWith('/ordenes')) return 'ordenes'
  if (path.startsWith('/inventario')) return 'inventario'
  if (path.startsWith('/facturacion')) return 'facturacion'
  if (path.startsWith('/plan-separe')) return 'plan-separe'
  if (path.startsWith('/gastos')) return 'gastos'
  if (path.startsWith('/informes')) return 'informes'
  if (path.startsWith('/parametrizacion')) return 'parametrizacion'
  if (path.startsWith('/usuarios')) return 'usuarios'
  
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
  background: rgba(59, 130, 246, 0.1);
  border-left-color: #3b82f6;
}

.nav-link-inventario:hover {
  background: rgba(99, 102, 241, 0.1);
  border-left-color: #6366f1;
}

.nav-link-facturacion:hover {
  background: rgba(16, 185, 129, 0.1);
  border-left-color: #10b981;
}

.nav-link-plansepare:hover {
  background: rgba(245, 158, 11, 0.1);
  border-left-color: #f59e0b;
}

.nav-link-ordenes:hover {
  background: rgba(249, 115, 22, 0.1);
  border-left-color: #f97316;
}

.nav-link-tecnicos:hover {
  background: rgba(20, 184, 166, 0.1);
  border-left-color: #14b8a6;
}

.nav-link-gastos:hover {
  background: rgba(239, 68, 68, 0.1);
  border-left-color: #ef4444;
}

.nav-link-informes:hover {
  background: rgba(168, 85, 247, 0.1);
  border-left-color: #a855f7;
}

.nav-link-parametrizacion:hover {
  background: rgba(6, 182, 212, 0.1);
  border-left-color: #06b6d4;
}

.nav-link-usuarios:hover {
  background: rgba(236, 72, 153, 0.1);
  border-left-color: #ec4899;
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
  color: #3b82f6;
}

.indigo-icon {
  color: #6366f1;
}

.green-icon {
  color: #10b981;
}

.yellow-icon {
  color: #f59e0b;
}

.orange-icon {
  color: #f97316;
}

.teal-icon {
  color: #14b8a6;
}

.red-icon {
  color: #ef4444;
}

.purple-icon {
  color: #a855f7;
}

.cyan-icon {
  color: #06b6d4;
}

.pink-icon {
  color: #ec4899;
}

.nav-text {
  font-size: 14px;
  font-weight: 500;
  flex: 1;
}

/* Indicadores de elemento activo por color */
.nav-indicator {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  margin-left: 8px;
}

.blue-indicator {
  background: #3b82f6;
  animation: pulse-blue 2s infinite;
}

.indigo-indicator {
  background: #6366f1;
  animation: pulse-indigo 2s infinite;
}

.green-indicator {
  background: #10b981;
  animation: pulse-green 2s infinite;
}

.yellow-indicator {
  background: #f59e0b;
  animation: pulse-yellow 2s infinite;
}

.orange-indicator {
  background: #f97316;
  animation: pulse-orange 2s infinite;
}

.teal-indicator {
  background: #14b8a6;
  animation: pulse-teal 2s infinite;
}

.red-indicator {
  background: #ef4444;
  animation: pulse-red 2s infinite;
}

.purple-indicator {
  background: #a855f7;
  animation: pulse-purple 2s infinite;
}

.cyan-indicator {
  background: #06b6d4;
  animation: pulse-cyan 2s infinite;
}

.pink-indicator {
  background: #ec4899;
  animation: pulse-pink 2s infinite;
}

/* Animaciones de pulso para cada color */
@keyframes pulse-blue {
  0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
  70% { box-shadow: 0 0 0 4px rgba(59, 130, 246, 0); }
  100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
}

@keyframes pulse-indigo {
  0% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.7); }
  70% { box-shadow: 0 0 0 4px rgba(99, 102, 241, 0); }
  100% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0); }
}

@keyframes pulse-green {
  0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
  70% { box-shadow: 0 0 0 4px rgba(16, 185, 129, 0); }
  100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

@keyframes pulse-yellow {
  0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); }
  70% { box-shadow: 0 0 0 4px rgba(245, 158, 11, 0); }
  100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
}

@keyframes pulse-orange {
  0% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.7); }
  70% { box-shadow: 0 0 0 4px rgba(249, 115, 22, 0); }
  100% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0); }
}

@keyframes pulse-teal {
  0% { box-shadow: 0 0 0 0 rgba(20, 184, 166, 0.7); }
  70% { box-shadow: 0 0 0 4px rgba(20, 184, 166, 0); }
  100% { box-shadow: 0 0 0 0 rgba(20, 184, 166, 0); }
}

@keyframes pulse-red {
  0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
  70% { box-shadow: 0 0 0 4px rgba(239, 68, 68, 0); }
  100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
}

@keyframes pulse-purple {
  0% { box-shadow: 0 0 0 0 rgba(168, 85, 247, 0.7); }
  70% { box-shadow: 0 0 0 4px rgba(168, 85, 247, 0); }
  100% { box-shadow: 0 0 0 0 rgba(168, 85, 247, 0); }
}

@keyframes pulse-cyan {
  0% { box-shadow: 0 0 0 0 rgba(6, 182, 212, 0.7); }
  70% { box-shadow: 0 0 0 4px rgba(6, 182, 212, 0); }
  100% { box-shadow: 0 0 0 0 rgba(6, 182, 212, 0); }
}

@keyframes pulse-pink {
  0% { box-shadow: 0 0 0 0 rgba(236, 72, 153, 0.7); }
  70% { box-shadow: 0 0 0 4px rgba(236, 72, 153, 0); }
  100% { box-shadow: 0 0 0 0 rgba(236, 72, 153, 0); }
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