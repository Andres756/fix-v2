<template>
  <div class="config-container">
    <!-- Panel Principal -->
    <div class="main-panel" :class="{ 'slide-out': activeModule !== null }">
      <header class="header">
        <div class="header-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="3"/>
            <path d="M12 1v6m0 6v6m11-7h-6m-6 0H1"/>
          </svg>
        </div>
        <div>
          <h1>Configuración de Parámetros</h1>
          <p>Administra y configura los parámetros principales del sistema</p>
        </div>
      </header>

      <div class="modules-grid">
        <!-- Inventario -->
        <div 
          class="module-card inventario"
          @click="openModule('inventario')"
          @mouseenter="hoveredModule = 'inventario'"
          @mouseleave="hoveredModule = null"
        >
          <div class="module-icon inventario">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
              <polyline points="3.27,6.96 12,12.01 20.73,6.96"/>
              <line x1="12" y1="22.08" x2="12" y2="12"/>
            </svg>
          </div>
          <h3>Inventario</h3>
          <p>Gestiona categorías, proveedores, unidades de medida y configuraciones de stock</p>
          <div class="arrow" :class="{ 'visible': hoveredModule === 'inventario' }">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="m9 18 6-6-6-6"/>
            </svg>
          </div>
        </div>

        <!-- Facturación -->
        <div 
          class="module-card facturacion"
          @click="openModule('facturacion')"
          @mouseenter="hoveredModule = 'facturacion'"
          @mouseleave="hoveredModule = null"
        >
          <div class="module-icon facturacion">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14,2 14,8 20,8"/>
              <line x1="16" y1="13" x2="8" y2="13"/>
              <line x1="16" y1="17" x2="8" y2="17"/>
              <polyline points="10,9 9,9 8,9"/>
            </svg>
          </div>
          <h3>Facturación</h3>
          <p>Configura impuestos, formas de pago, numeraciones y parámetros fiscales</p>
          <div class="arrow" :class="{ 'visible': hoveredModule === 'facturacion' }">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="m9 18 6-6-6-6"/>
            </svg>
          </div>
        </div>

        <!-- Organización -->
        <div 
          class="module-card organizacion"
          @click="openModule('organizacion')"
          @mouseenter="hoveredModule = 'organizacion'"
          @mouseleave="hoveredModule = null"
        >
          <div class="module-icon organizacion">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
              <rect x="7" y="7" width="3" height="9"/>
              <rect x="14" y="7" width="3" height="5"/>
            </svg>
          </div>
          <h3>Organización</h3>
          <p>Administra sedes, áreas, cargos y estructura organizacional</p>
          <div class="arrow" :class="{ 'visible': hoveredModule === 'organizacion' }">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="m9 18 6-6-6-6"/>
            </svg>
          </div>
        </div>

        <!-- Órdenes de Servicio -->
        <div 
          class="module-card ordenes"
          @click="openModule('ordenes')"
          @mouseenter="hoveredModule = 'ordenes'"
          @mouseleave="hoveredModule = null"
        >
          <div class="module-icon ordenes">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14,2 14,8 20,8"/>
              <line x1="9" y1="15" x2="15" y2="15"/>
            </svg>
          </div>
          <h3>Órdenes de Servicio</h3>
          <p>Define tipos de orden, estados, workflows y responsables</p>
          <div class="arrow" :class="{ 'visible': hoveredModule === 'ordenes' }">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="m9 18 6-6-6-6"/>
            </svg>
          </div>
        </div>

        <!-- Usuarios y Roles -->
        <div 
          class="module-card usuarios"
          @click="openModule('usuarios')"
          @mouseenter="hoveredModule = 'usuarios'"
          @mouseleave="hoveredModule = null"
        >
          <div class="module-icon usuarios">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
          </div>
          <h3>Usuarios y Roles</h3>
          <p>Gestiona roles, permisos, tipos de usuario y control de acceso</p>
          <div class="arrow" :class="{ 'visible': hoveredModule === 'usuarios' }">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="m9 18 6-6-6-6"/>
            </svg>
          </div>
        </div>

        <!-- Próximamente -->
        <div class="module-card coming-soon">
          <div class="module-icon coming-soon-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <polyline points="12,6 12,12 16,14"/>
            </svg>
          </div>
          <h3>Próximamente</h3>
          <p>Nuevos módulos de configuración estarán disponibles pronto</p>
        </div>
      </div>
    </div>

    <!-- Panel de Opciones del Módulo -->
    <div class="module-panel" :class="{ 'slide-in': activeModule !== null }">
      <!-- Header con botón de regreso -->
      <header class="module-header">
        <div class="back-section" @click="closeModule">
          <svg class="back-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="m15 18-6-6 6-6"/>
          </svg>
          <span>Volver</span>
        </div>
        <div class="module-info">
          <div class="module-title-icon" :class="getModuleClass()">
            <component :is="getModuleIconComponent()" />
          </div>
          <div>
            <h1>Configuración de {{ getModuleTitle() }}</h1>
            <p>{{ getModuleDescription() }}</p>
          </div>
        </div>
      </header>

      <!-- Grid de opciones específicas del módulo -->
      <div class="options-grid">
        <!-- Inventario Options -->
        <template v-if="activeModule === 'inventario'">
          <div class="option-card">
            <div class="option-icon" style="background: linear-gradient(135deg, #3b82f6, #1e40af);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
              </svg>
            </div>
            <h3>Categorías</h3>
            <p>Administra las categorías de productos y servicios</p>
          </div>
          <div class="option-card">
            <div class="option-icon" style="background: linear-gradient(135deg, #10b981, #047857);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 12l2 2 4-4"/>
                <circle cx="21" cy="12" r="9"/>
              </svg>
            </div>
            <h3>Proveedores</h3>
            <p>Gestiona la información de proveedores y distribuidores</p>
          </div>
          <div class="option-card">
            <div class="option-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
              </svg>
            </div>
            <h3>Unidades de Medida</h3>
            <p>Define las unidades de medida para productos</p>
          </div>
          <div class="option-card">
            <div class="option-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                <line x1="8" y1="21" x2="16" y2="21"/>
                <line x1="12" y1="17" x2="12" y2="21"/>
              </svg>
            </div>
            <h3>Almacenes</h3>
            <p>Configura almacenes y ubicaciones de stock</p>
          </div>
          <div class="option-card">
            <div class="option-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
              </svg>
            </div>
            <h3>Stock Mínimo</h3>
            <p>Establece alertas y niveles mínimos de inventario</p>
          </div>
          <div class="option-card coming-soon-card" @click="closeModule">
            <div class="option-icon" style="background: linear-gradient(135deg, #9ca3af, #6b7280);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="m15 18-6-6 6-6"/>
              </svg>
            </div>
            <h3>Volver</h3>
            <p>Regresar al menú principal</p>
          </div>
        </template>

        <!-- Facturación Options -->
        <template v-if="activeModule === 'facturacion'">
          <div class="option-card">
            <div class="option-icon" style="background: linear-gradient(135deg, #10b981, #047857);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                <path d="m9 9 5 12 1.774-5.226L21 14 9 9z"/>
              </svg>
            </div>
            <h3>Impuestos</h3>
            <p>Configura tipos de impuestos y tarifas fiscales</p>
          </div>
          <div class="option-card">
            <div class="option-icon" style="background: linear-gradient(135deg, #3b82f6, #1e40af);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="1" y="3" width="15" height="13"/>
                <polygon points="16,8 20,8 23,11 23,16 16,16 16,8"/>
                <circle cx="5.5" cy="18.5" r="2.5"/>
                <circle cx="18.5" cy="18.5" r="2.5"/>
              </svg>
            </div>
            <h3>Formas de Pago</h3>
            <p>Administra métodos y formas de pago disponibles</p>
          </div>
          <div class="option-card">
            <div class="option-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9,11 12,14 22,4"/>
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
              </svg>
            </div>
            <h3>Numeración</h3>
            <p>Define numeración de facturas y documentos</p>
          </div>
          <div class="option-card">
            <div class="option-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14,2 14,8 20,8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
              </svg>
            </div>
            <h3>Plantillas</h3>
            <p>Personaliza plantillas de facturas y reportes</p>
          </div>
          <div class="option-card">
            <div class="option-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="1" x2="12" y2="23"/>
                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
              </svg>
            </div>
            <h3>Monedas</h3>
            <p>Gestiona monedas y tipos de cambio</p>
          </div>
          <div class="option-card coming-soon-card" @click="closeModule">
            <div class="option-icon" style="background: linear-gradient(135deg, #9ca3af, #6b7280);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="m15 18-6-6 6-6"/>
              </svg>
            </div>
            <h3>Volver</h3>
            <p>Regresar al menú principal</p>
          </div>
        </template>

        <!-- Organización Options -->
        <template v-if="activeModule === 'organizacion'">
          <div class="option-card">
            <div class="option-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9,22 9,12 15,12 15,22"/>
              </svg>
            </div>
            <h3>Sedes</h3>
            <p>Administra sedes y sucursales de la empresa</p>
          </div>
          <div class="option-card">
            <div class="option-icon" style="background: linear-gradient(135deg, #3b82f6, #1e40af);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                <rect x="7" y="7" width="3" height="9"/>
                <rect x="14" y="7" width="3" height="5"/>
              </svg>
            </div>
            <h3>Áreas</h3>
            <p>Define áreas de trabajo y departamentos</p>
          </div>
          <div class="option-card">
            <div class="option-icon" style="background: linear-gradient(135deg, #10b981, #047857);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <polyline points="16,11 18,13 22,9"/>
              </svg>
            </div>
            <h3>Cargos</h3>
            <p>Gestiona cargos y posiciones laborales</p>
          </div>
          <div class="option-card coming-soon-card" @click="closeModule">
            <div class="option-icon" style="background: linear-gradient(135deg, #9ca3af, #6b7280);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="m15 18-6-6 6-6"/>
              </svg>
            </div>
            <h3>Volver</h3>
            <p>Regresar al menú principal</p>
          </div>
        </template>

        <!-- Órdenes de Servicio Options -->
        <template v-if="activeModule === 'ordenes'">
          <div class="option-card">
            <div class="option-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14,2 14,8 20,8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
              </svg>
            </div>
            <h3>Tipos de Orden</h3>
            <p>Define tipos de órdenes de servicio</p>
          </div>
          <div class="option-card">
            <div class="option-icon" style="background: linear-gradient(135deg, #10b981, #047857);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9,11 12,14 22,4"/>
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
              </svg>
            </div>
            <h3>Estados</h3>
            <p>Configura estados y flujos de trabajo</p>
          </div>
          <div class="option-card">
            <div class="option-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
              </svg>
            </div>
            <h3>Responsables</h3>
            <p>Asigna responsables y técnicos</p>
          </div>
          <div class="option-card coming-soon-card" @click="closeModule">
            <div class="option-icon" style="background: linear-gradient(135deg, #9ca3af, #6b7280);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="m15 18-6-6 6-6"/>
              </svg>
            </div>
            <h3>Volver</h3>
            <p>Regresar al menú principal</p>
          </div>
        </template>

        <!-- Usuarios y Roles Options -->
        <template v-if="activeModule === 'usuarios'">
          <div class="option-card">
            <div class="option-icon" style="background: linear-gradient(135deg, #6366f1, #4f46e5);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
              </svg>
            </div>
            <h3>Roles</h3>
            <p>Gestiona roles y permisos del sistema</p>
          </div>
          <div class="option-card">
            <div class="option-icon" style="background: linear-gradient(135deg, #10b981, #047857);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <circle cx="12" cy="16" r="1"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
              </svg>
            </div>
            <h3>Permisos</h3>
            <p>Configura permisos y control de acceso</p>
          </div>
          <div class="option-card">
            <div class="option-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
            </div>
            <h3>Tipos de Usuario</h3>
            <p>Define tipos y categorías de usuarios</p>
          </div>
          <div class="option-card coming-soon-card" @click="closeModule">
            <div class="option-icon" style="background: linear-gradient(135deg, #9ca3af, #6b7280);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="m15 18-6-6 6-6"/>
              </svg>
            </div>
            <h3>Volver</h3>
            <p>Regresar al menú principal</p>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, h } from 'vue'

type ModuleType = 'inventario' | 'facturacion' | 'organizacion' | 'ordenes' | 'usuarios' | null

const activeModule = ref<ModuleType>(null)
const hoveredModule = ref<string | null>(null)

const openModule = (module: ModuleType) => {
  activeModule.value = module
}

const closeModule = () => {
  activeModule.value = null
}

const getModuleTitle = (): string => {
  const titles = {
    inventario: 'Inventario',
    facturacion: 'Facturación',
    organizacion: 'Organización',
    ordenes: 'Órdenes de Servicio',
    usuarios: 'Usuarios y Roles'
  }
  return activeModule.value ? titles[activeModule.value] : ''
}

const getModuleDescription = (): string => {
  const descriptions = {
    inventario: 'Gestiona todos los parámetros relacionados con el inventario',
    facturacion: 'Configura impuestos, formas de pago, numeraciones y parámetros fiscales',
    organizacion: 'Administra sedes, áreas, cargos y estructura organizacional',
    ordenes: 'Define tipos de orden, estados, workflows y responsables',
    usuarios: 'Gestiona roles, permisos, tipos de usuario y control de acceso'
  }
  return activeModule.value ? descriptions[activeModule.value] : ''
}

const getModuleClass = (): string => {
  return activeModule.value ? activeModule.value : ''
}

const getModuleIconComponent = () => {
  const icons = {
    inventario: () => h('svg', {
      viewBox: "0 0 24 24",
      fill: "none",
      stroke: "currentColor",
      strokeWidth: "2"
    }, [
      h('path', { d: "M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" }),
      h('polyline', { points: "3.27,6.96 12,12.01 20.73,6.96" }),
      h('line', { x1: "12", y1: "22.08", x2: "12", y2: "12" })
    ]),
    facturacion: () => h('svg', {
      viewBox: "0 0 24 24",
      fill: "none",
      stroke: "currentColor",
      strokeWidth: "2"
    }, [
      h('path', { d: "M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" }),
      h('polyline', { points: "14,2 14,8 20,8" }),
      h('line', { x1: "16", y1: "13", x2: "8", y2: "13" }),
      h('line', { x1: "16", y1: "17", x2: "8", y2: "17" })
    ]),
    organizacion: () => h('svg', {
      viewBox: "0 0 24 24",
      fill: "none",
      stroke: "currentColor",
      strokeWidth: "2"
    }, [
      h('rect', { x: "3", y: "3", width: "18", height: "18", rx: "2", ry: "2" }),
      h('rect', { x: "7", y: "7", width: "3", height: "9" }),
      h('rect', { x: "14", y: "7", width: "3", height: "5" })
    ]),
    ordenes: () => h('svg', {
      viewBox: "0 0 24 24",
      fill: "none",
      stroke: "currentColor",
      strokeWidth: "2"
    }, [
      h('path', { d: "M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" }),
      h('polyline', { points: "14,2 14,8 20,8" }),
      h('line', { x1: "9", y1: "15", x2: "15", y2: "15" })
    ]),
    usuarios: () => h('svg', {
      viewBox: "0 0 24 24",
      fill: "none",
      stroke: "currentColor",
      strokeWidth: "2"
    }, [
      h('path', { d: "M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" }),
      h('circle', { cx: "9", cy: "7", r: "4" }),
      h('path', { d: "M22 21v-2a4 4 0 0 0-3-3.87" }),
      h('path', { d: "M16 3.13a4 4 0 0 1 0 7.75" })
    ])
  }
  return activeModule.value ? icons[activeModule.value] : () => h('div')
}
</script>

<style scoped>
/* Base Container */
.config-container {
  position: relative;
  width: 100%;
  min-height: 100vh;
  background-color: transparent;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  overflow-x: hidden;
}

/* Panel Transitions */
.main-panel {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  min-height: 100vh;
  padding: 1rem;
  transition: transform 0.4s ease;
  background-color: transparent;
}

.main-panel.slide-out {
  transform: translateX(-100%);
}

.module-panel {
  position: absolute;
  top: 0;
  left: 100%;
  width: 100%;
  min-height: 100vh;
  padding: 1rem;
  background-color: transparent;
  transition: transform 0.4s ease;
}

.module-panel.slide-in {
  transform: translateX(-100%);
}

/* Headers */
.header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.module-header {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.back-section {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  color: #6b7280;
  font-size: 0.875rem;
  transition: color 0.2s ease;
  margin-top: 0.25rem;
  white-space: nowrap;
}

.back-section:hover {
  color: #374151;
}

.back-icon {
  width: 1rem;
  height: 1rem;
  flex-shrink: 0;
}

.module-info {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.header-icon, .module-title-icon {
  width: 3rem;
  height: 3rem;
  border-radius: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  flex-shrink: 0;
}

.header-icon {
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
}

.module-title-icon.inventario {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
}

.module-title-icon.facturacion {
  background: linear-gradient(135deg, #10b981, #047857);
}

.module-title-icon.organizacion {
  background: linear-gradient(135deg, #8b5cf6, #7c3aed);
}

.module-title-icon.ordenes {
  background: linear-gradient(135deg, #f59e0b, #d97706);
}

.module-title-icon.usuarios {
  background: linear-gradient(135deg, #6366f1, #4f46e5);
}

.header-icon svg, .module-title-icon svg {
  width: 1.5rem;
  height: 1.5rem;
}

.header h1, .module-header h1 {
  margin: 0;
  font-size: clamp(1.25rem, 4vw, 1.5rem);
  font-weight: 600;
  color: #1f2937;
}

.header p, .module-header p {
  margin: 0;
  color: #6b7280;
  font-size: clamp(0.75rem, 3vw, 0.875rem);
  line-height: 1.4;
}

/* Grid Layouts */
.modules-grid, .options-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

/* Cards */
.module-card, .option-card {
  background: white;
  border-radius: 1rem;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  transition: all 0.2s ease;
  position: relative;
  border: 1px solid #e5e7eb;
  min-height: 140px;
}

.module-card:hover, .option-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.module-card.inventario:hover {
  border-color: #3b82f6;
}

.module-card.facturacion:hover {
  border-color: #10b981;
}

.module-card.organizacion:hover {
  border-color: #8b5cf6;
}

.module-card.ordenes:hover {
  border-color: #f59e0b;
}

.module-card.usuarios:hover {
  border-color: #6366f1;
}

.module-card.coming-soon, .option-card.coming-soon-card {
  opacity: 0.6;
  cursor: default;
  border: 2px dashed #d1d5db;
  background: #f9fafb;
}

.module-card.coming-soon:hover, .option-card.coming-soon-card:hover {
  transform: none;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.option-card.coming-soon-card:hover {
  cursor: pointer;
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  opacity: 1;
  border: 1px solid #6b7280;
}

/* Icons */
.module-icon, .option-icon {
  width: 3rem;
  height: 3rem;
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
  color: white;
  flex-shrink: 0;
}

.module-icon.inventario {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
}

.module-icon.facturacion {
  background: linear-gradient(135deg, #10b981, #047857);
}

.module-icon.organizacion {
  background: linear-gradient(135deg, #8b5cf6, #7c3aed);
}

.module-icon.ordenes {
  background: linear-gradient(135deg, #f59e0b, #d97706);
}

.module-icon.usuarios {
  background: linear-gradient(135deg, #6366f1, #4f46e5);
}

.module-icon.coming-soon-icon {
  background: linear-gradient(135deg, #9ca3af, #6b7280);
}

.module-icon svg, .option-icon svg {
  width: 1.5rem;
  height: 1.5rem;
}

/* Text */
.module-card h3, .option-card h3 {
  margin: 0 0 0.5rem 0;
  font-size: clamp(1rem, 3vw, 1.125rem);
  font-weight: 600;
  color: #1f2937;
  line-height: 1.3;
}

.module-card p, .option-card p {
  margin: 0;
  color: #6b7280;
  font-size: clamp(0.75rem, 2.5vw, 0.875rem);
  line-height: 1.4;
}

/* Arrow Indicator */
.arrow {
  position: absolute;
  top: 50%;
  right: 1rem;
  transform: translateY(-50%);
  opacity: 0;
  transition: opacity 0.2s ease;
  color: #9ca3af;
}

.arrow.visible {
  opacity: 1;
}

.arrow svg {
  width: 1.25rem;
  height: 1.25rem;
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .main-panel, .module-panel {
    padding: 1rem 0.75rem;
  }
  
  .header, .module-header {
    margin-bottom: 1.5rem;
    gap: 0.75rem;
  }
  
  .header-icon, .module-title-icon {
    width: 2.5rem;
    height: 2.5rem;
  }
  
  .header-icon svg, .module-title-icon svg {
    width: 1.25rem;
    height: 1.25rem;
  }
  
  .modules-grid, .options-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .module-card, .option-card {
    padding: 1.25rem;
    min-height: 120px;
  }
  
  .module-icon, .option-icon {
    width: 2.5rem;
    height: 2.5rem;
    margin-bottom: 0.75rem;
  }
  
  .module-icon svg, .option-icon svg {
    width: 1.25rem;
    height: 1.25rem;
  }
  
  .arrow {
    display: none;
  }
  
  .windows-activation {
    bottom: 0.5rem;
    right: 0.5rem;
    left: 0.5rem;
    max-width: none;
    font-size: 0.7rem;
  }
}

/* Tablet Responsive */
@media (min-width: 769px) and (max-width: 1024px) {
  .modules-grid, .options-grid {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.25rem;
  }
  
  .main-panel, .module-panel {
    padding: 1.5rem;
  }
}

/* Large screens */
@media (min-width: 1200px) {
  .modules-grid, .options-grid {
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    max-width: 1400px;
    margin-left: auto;
    margin-right: auto;
  }
  
  .main-panel, .module-panel {
    padding: 2rem;
    max-width: 1600px;
    margin-left: auto;
    margin-right: auto;
  }
}

/* Ultra-wide screens */
@media (min-width: 1600px) {
  .modules-grid, .options-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

/* Touch devices */
@media (hover: none) and (pointer: coarse) {
  .arrow {
    display: none;
  }
  
  .module-card:hover, .option-card:hover {
    transform: none;
  }
  
  .module-card:active, .option-card:active {
    transform: scale(0.98);
    transition: transform 0.1s ease;
  }
}
</style>