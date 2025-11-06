<template>
  <nav class="navbar">
    <div class="navbar-left">
      <button 
        class="sidebar-toggle" 
        @click="$emit('toggleSidebar')"
        :class="{ active: sidebarOpen }"
      >
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <line x1="3" y1="6" x2="21" y2="6" stroke="currentColor" stroke-width="2"/>
          <line x1="3" y1="12" x2="21" y2="12" stroke="currentColor" stroke-width="2"/>
          <line x1="3" y1="18" x2="21" y2="18" stroke="currentColor" stroke-width="2"/>
        </svg>
      </button>
      <div class="page-title">
        <h1>{{ pageTitle }}</h1>
      </div>
    </div>

    <div class="navbar-right">
      <!-- Search Bar -->
      <div class="search-container">
        <div class="search-input">
          <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none">
            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
            <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2"/>
          </svg>
          <input 
            type="text" 
            placeholder="Buscar..."
            v-model="searchQuery"
            @input="handleSearch"
          />
        </div>
      </div>

      <!-- Notifications -->
      <div class="notification-container">
        <button class="notification-btn" @click="toggleNotifications">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" stroke="currentColor" stroke-width="2"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="2"/>
          </svg>
          <span class="notification-badge" v-if="notificationCount > 0">
            {{ notificationCount }}
          </span>
        </button>

        <!-- Notifications Dropdown -->
        <div class="notifications-dropdown" v-if="showNotifications" @click.stop>
          <div class="dropdown-header">
            <h3>Notificaciones</h3>
            <button class="mark-all-read" @click="markAllAsRead">
              Marcar todas como leídas
            </button>
          </div>
          <div class="notifications-list">
            <div 
              v-for="notification in notifications" 
              :key="notification.id"
              class="notification-item"
              :class="{ unread: !notification.read }"
            >
              <div class="notification-icon" :class="notification.type">
                <svg v-if="notification.type === 'success'" width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <polyline points="20,6 9,17 4,12" stroke="currentColor" stroke-width="2"/>
                </svg>
                <svg v-else-if="notification.type === 'warning'" width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" stroke="currentColor" stroke-width="2"/>
                  <line x1="12" y1="9" x2="12" y2="13" stroke="currentColor" stroke-width="2"/>
                  <line x1="12" y1="17" x2="12.01" y2="17" stroke="currentColor" stroke-width="2"/>
                </svg>
                <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                  <line x1="12" y1="8" x2="12" y2="12" stroke="currentColor" stroke-width="2"/>
                  <line x1="12" y1="16" x2="12.01" y2="16" stroke="currentColor" stroke-width="2"/>
                </svg>
              </div>
              <div class="notification-content">
                <p class="notification-title">{{ notification.title }}</p>
                <p class="notification-time">{{ notification.time }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- User Profile -->
      <div class="user-profile">
        <button class="profile-btn" @click="toggleUserMenu">
          <div class="user-avatar">
            <span>{{ initials }}</span>
          </div>
          <div class="user-info">
            <span class="user-name">{{ userName }}</span>
            <span class="user-role">Administrador</span>
          </div>
          <svg class="dropdown-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none">
            <polyline points="6,9 12,15 18,9" stroke="currentColor" stroke-width="2"/>
          </svg>
        </button>

        
        <div class="user-dropdown" v-if="showUserMenu" @click.stop>
<!-- User Dropdown 
          <div class="dropdown-item" @click="goToProfile">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2"/>
              <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
            </svg>
            <span>Mi Perfil</span>
          </div>
          <div class="dropdown-item" @click="goToSettings">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
              <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
              <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" stroke="currentColor" stroke-width="2"/>
            </svg>
            <span>Configuración</span>
          </div>-->

          <div class="dropdown-item logout" @click="logout">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" stroke="currentColor" stroke-width="2"/>
              <polyline points="16,17 21,12 16,7" stroke="currentColor" stroke-width="2"/>
              <line x1="21" y1="12" x2="9" y2="12" stroke="currentColor" stroke-width="2"/>
            </svg>
            <span>Cerrar Sesión</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Click outside to close dropdowns -->
    <div 
      v-if="showNotifications || showUserMenu" 
      class="dropdown-overlay" 
      @click="closeDropdowns"
    ></div>
  </nav>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import http from '../shared/api/http' // ajusta la ruta si no usas alias '@'
import { getUser, setUser, isAuthenticated, clearAuth } from '../auth/auth'

interface Props { sidebarOpen: boolean }
interface Notification {
  id: number; title: string; time: string;
  type: 'success' | 'warning' | 'info'; read: boolean
}
interface AuthUser { id: number; name: string; email: string }

defineProps<Props>()
defineEmits<{ toggleSidebar: [] }>()

const router = useRouter()
const route = useRoute()

const searchQuery = ref('')
const showNotifications = ref(false)
const showUserMenu = ref(false)

const user = ref<AuthUser | null>(getUser())

const userName = computed(() => user.value?.name ?? 'Usuario')
const initials = computed(() => {
  const n = (user.value?.name || '').trim()
  const parts = n.split(/\s+/).slice(0, 2)
  const ini = parts.map(p => p[0]?.toUpperCase() || '').join('')
  return ini || 'US'
})

const notifications = ref<Notification[]>([
  { id: 1, title: 'Nueva venta registrada - iPhone 14 Pro - $999', time: 'Hace 5 minutos', type: 'success', read: false },
  { id: 2, title: 'Producto agregado - MacBook Air M2', time: 'Hace 15 minutos', type: 'info', read: false },
  { id: 3, title: 'Stock bajo detectado - AirPods Pro - 3 unidades', time: 'Hace 1 hora', type: 'warning', read: true },
])

const pageTitle = computed(() => {
  const titles: Record<string, string> = {
    '/': 'Dashboard',
    '/inventario': 'Inventario',
    '/facturacion': 'Facturación',
    '/plan-separe': 'Plan Separe',  // 👈 AGREGAR ESTA LÍNEA
    '/ordenes': 'Órdenes de Servicio',
    '/ordenes/tecnicos': 'Tecnicos',
    '/gastos': 'Gastos',
    '/informes': 'Informes',
    '/parametrizacion': 'Parametrización',
    '/usuarios': 'Usuarios',
  }
  return titles[route.path] || 'Dashboard'
})

const notificationCount = computed(() => notifications.value.filter(n => !n.read).length)

const toggleNotifications = () => { showNotifications.value = !showNotifications.value; showUserMenu.value = false }
const toggleUserMenu     = () => { showUserMenu.value = !showUserMenu.value; showNotifications.value = false }
const closeDropdowns     = () => { showNotifications.value = false; showUserMenu.value = false }

const handleSearch = () => { console.log('Searching for:', searchQuery.value) }
const markAllAsRead = () => { notifications.value.forEach(n => (n.read = true)) }
const goToProfile   = () => { closeDropdowns() }
const goToSettings  = () => { closeDropdowns() }

const logout = async () => {
  closeDropdowns()
  try { await http.post('/logout') } catch {}
  clearAuth()
  router.push('/login')
}

// Click fuera para cerrar dropdowns
const handleClickOutside = (event: Event) => {
  const target = event.target as HTMLElement
  if (!target.closest('.notification-container') && !target.closest('.user-profile')) closeDropdowns()
}

onMounted(async () => {
  document.addEventListener('click', handleClickOutside)

  // Si hay token pero aún no tenemos el usuario en memoria, recupéralo
  if (isAuthenticated() && !user.value) {
    try {
      const { data } = await http.get('/user') // tu endpoint protegido
      user.value = data
      setUser(data)
    } catch {
      clearAuth()
    }
  }
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.navbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 70px;
  background: white;
  border-bottom: 1px solid #e5e7eb;
  padding: 0 24px;
  position: relative;
  z-index: 100;
}

.navbar-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.sidebar-toggle {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  background: transparent;
  border: none;
  border-radius: 8px;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.2s ease;
}

.sidebar-toggle:hover {
  background: #f3f4f6;
  color: #374151;
}

.sidebar-toggle.active {
  background: #667eea;
  color: white;
}

.page-title h1 {
  margin: 0;
  font-size: 24px;
  font-weight: 600;
  color: #111827;
}

.navbar-right {
  display: flex;
  align-items: center;
  gap: 20px;
}

.search-container {
  position: relative;
}

.search-input {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 12px;
  color: #9ca3af;
  z-index: 1;
}

.search-input input {
  width: 300px;
  height: 40px;
  padding: 0 16px 0 44px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  background: #f9fafb;
  transition: all 0.2s ease;
}

.search-input input:focus {
  outline: none;
  border-color: #667eea;
  background: white;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.notification-container {
  position: relative;
}

.notification-btn {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  background: transparent;
  border: none;
  border-radius: 8px;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.2s ease;
}

.notification-btn:hover {
  background: #f3f4f6;
  color: #374151;
}

.notification-badge {
  position: absolute;
  top: -2px;
  right: -2px;
  width: 20px;
  height: 20px;
  background: #ef4444;
  color: white;
  border-radius: 50%;
  font-size: 11px;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid white;
}

.notifications-dropdown {
  position: absolute;
  top: 50px;
  right: 0;
  width: 380px;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  z-index: 200;
}

.dropdown-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  border-bottom: 1px solid #e5e7eb;
}

.dropdown-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: #111827;
}

.mark-all-read {
  background: none;
  border: none;
  color: #667eea;
  font-size: 12px;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 4px;
  transition: background 0.2s ease;
}

.mark-all-read:hover {
  background: #f0f0ff;
}

.notifications-list {
  max-height: 400px;
  overflow-y: auto;
}

.notification-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 16px 20px;
  border-bottom: 1px solid #f3f4f6;
  transition: background 0.2s ease;
}

.notification-item:hover {
  background: #f9fafb;
}

.notification-item.unread {
  background: #f0f9ff;
}

.notification-item:last-child {
  border-bottom: none;
}

.notification-icon {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.notification-icon.success {
  background: #dcfce7;
  color: #16a34a;
}

.notification-icon.warning {
  background: #fef3c7;
  color: #d97706;
}

.notification-icon.info {
  background: #dbeafe;
  color: #2563eb;
}

.notification-content {
  flex: 1;
  min-width: 0;
}

.notification-title {
  margin: 0 0 4px;
  font-size: 14px;
  font-weight: 500;
  color: #111827;
  line-height: 1.4;
}

.notification-time {
  margin: 0;
  font-size: 12px;
  color: #6b7280;
}

.user-profile {
  position: relative;
}

.profile-btn {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 8px 12px;
  background: transparent;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.2s ease;
}

.profile-btn:hover {
  background: #f3f4f6;
}

.user-avatar {
  width: 36px;
  height: 36px;
  background: #667eea;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 14px;
}

.user-info {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.user-name {
  font-size: 14px;
  font-weight: 600;
  color: #111827;
}

.user-role {
  font-size: 12px;
  color: #6b7280;
}

.dropdown-arrow {
  color: #9ca3af;
  transition: transform 0.2s ease;
}

.user-dropdown {
  position: absolute;
  top: 50px;
  right: 0;
  width: 200px;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  z-index: 200;
  padding: 8px 0;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  color: #374151;
  font-size: 14px;
  cursor: pointer;
  transition: background 0.2s ease;
}

.dropdown-item:hover {
  background: #f3f4f6;
}

.dropdown-item.logout {
  color: #dc2626;
}

.dropdown-item.logout:hover {
  background: #fef2f2;
}

.dropdown-divider {
  height: 1px;
  background: #e5e7eb;
  margin: 8px 0;
}

.dropdown-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 150;
}

@media (max-width: 768px) {
  .navbar {
    padding: 0 16px;
  }
  
  .search-input input {
    width: 200px;
  }
  
  .user-info {
    display: none;
  }
  
  .notifications-dropdown,
  .user-dropdown {
    right: -20px;
  }
  
  .notifications-dropdown {
    width: 320px;
  }
}

@media (max-width: 480px) {
  .search-container {
    display: none;
  }
  
  .navbar-right {
    gap: 12px;
  }
}
</style>