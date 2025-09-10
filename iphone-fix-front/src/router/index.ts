// src/router/index.ts
import { createRouter, createWebHistory } from 'vue-router'
import Login from '../components/Login.vue'
import Register from '../components/Register.vue'
import Dashboard from '../views/Dashboard.vue'
import Inventario from '../views/inventario/index.vue'
import Facturacion from '../views/facturacion/index.vue'
import Ordenes from '../views/ordenes-servicios/index.vue'
import Gastos from '../views/gastos/index.vue'
import Informes from '../views/Informes/index.vue'
import Parametrizacion from '../views/parametros/index.vue'
import Usuarios from '../views/usuarios/index.vue'
import { isAuthenticated } from '../auth/auth'

const routes = [
  { path: '/login', component: Login, meta: { guestOnly: true, hideChrome: true } }, // 👈 clave
  { path: '/register', component: Register, meta: { guestOnly: true, hideChrome: true } }, // 👈 clave
  { path: '/', redirect: '/dashboard' },
  { path: '/dashboard', component: Dashboard, meta: { requiresAuth: true } },
  { path: '/inventario', component: Inventario, meta: { requiresAuth: true } },
  { path: '/facturacion', component: Facturacion, meta: { requiresAuth: true } },
  { path: '/ordenes', component: Ordenes, meta: { requiresAuth: true } },
  { path: '/gastos', component: Gastos, meta: { requiresAuth: true } },
  { path: '/informes', component: Informes, meta: { requiresAuth: true } },
  { path: '/parametrizacion', component: Parametrizacion, meta: { requiresAuth: true } },
  { path: '/usuarios', component: Usuarios, meta: { requiresAuth: true } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// Middleware de protección
router.beforeEach((to, _from, next) => {
  const auth = isAuthenticated()

  if (to.meta.requiresAuth && !auth) {
    next('/login')
  } else if (to.meta.guestOnly && auth) {
    next('/dashboard')
  } else {
    next()
  }
})

export default router
