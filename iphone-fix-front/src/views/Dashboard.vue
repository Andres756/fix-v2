<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    <div class="max-w-7xl mx-auto space-y-6">
      <!-- Header -->
      <div class="bg-white rounded-2xl shadow-lg p-8 border-l-4 border-blue-600">
        <div class="flex items-start justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
              📊 Dashboard Personalizable
            </h1>
            <p class="text-gray-600 text-lg">
              Selecciona las métricas que deseas visualizar en tu panel de control
            </p>
          </div>
          <div class="text-right">
            <div class="text-sm text-gray-500">Última actualización</div>
            <div class="text-lg font-semibold text-gray-900">{{ fechaActual }}</div>
          </div>
        </div>
      </div>

      <!-- Filtros Globales -->
      <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
          </svg>
          Filtros de Tiempo
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <button
            v-for="periodo in periodos"
            :key="periodo.id"
            @click="periodoSeleccionado = periodo.id"
            :class="[
              'px-4 py-3 rounded-lg border-2 transition-all font-medium',
              periodoSeleccionado === periodo.id
                ? 'border-blue-600 bg-blue-50 text-blue-700'
                : 'border-gray-200 hover:border-blue-300 text-gray-700'
            ]"
          >
            {{ periodo.label }}
          </button>
        </div>
      </div>

      <!-- Sección: Métricas de Ventas -->
      <div class="space-y-4">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h2 class="text-2xl font-bold text-gray-900">💰 Métricas de Ventas e Ingresos</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Card 1: Ingresos Totales -->
          <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform cursor-pointer">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <span class="text-sm font-semibold bg-white/20 px-3 py-1 rounded-full">+12%</span>
            </div>
            <div class="text-3xl font-bold mb-1">$25,450,000</div>
            <div class="text-green-100 text-sm font-medium">Ingresos Totales</div>
            <div class="mt-3 text-xs text-green-100">Ventas + Servicios + Plan Separe</div>
          </div>

          <!-- Card 2: Ventas Directas -->
          <div class="bg-white rounded-xl shadow-md p-6 border-2 border-gray-200 hover:border-blue-400 transition-all cursor-pointer">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
              </div>
              <span class="text-sm font-semibold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">+8%</span>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-1">$18,200,000</div>
            <div class="text-gray-600 text-sm font-medium">Ventas Directas</div>
            <div class="mt-3 text-xs text-gray-500">145 productos vendidos</div>
          </div>

          <!-- Card 3: Servicios Técnicos -->
          <div class="bg-white rounded-xl shadow-md p-6 border-2 border-gray-200 hover:border-purple-400 transition-all cursor-pointer">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <span class="text-sm font-semibold text-purple-600 bg-purple-50 px-3 py-1 rounded-full">+15%</span>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-1">$5,800,000</div>
            <div class="text-gray-600 text-sm font-medium">Servicios Técnicos</div>
            <div class="mt-3 text-xs text-gray-500">68 reparaciones completadas</div>
          </div>

          <!-- Card 4: Plan Separe -->
          <div class="bg-white rounded-xl shadow-md p-6 border-2 border-gray-200 hover:border-orange-400 transition-all cursor-pointer">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </div>
              <span class="text-sm font-semibold text-orange-600 bg-orange-50 px-3 py-1 rounded-full">23 activos</span>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-1">$1,450,000</div>
            <div class="text-gray-600 text-sm font-medium">Abonos Plan Separe</div>
            <div class="mt-3 text-xs text-gray-500">$8,200,000 pendientes</div>
          </div>
        </div>
      </div>

      <!-- Sección: Inventario -->
      <div class="space-y-4">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
          </div>
          <h2 class="text-2xl font-bold text-gray-900">📦 Control de Inventario</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <!-- Valor Inventario -->
          <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-indigo-500">
            <div class="flex items-center justify-between mb-4">
              <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
              </div>
              <span class="text-xs text-gray-500">Actualizado hoy</span>
            </div>
            <div class="text-2xl font-bold text-gray-900 mb-1">$45,680,000</div>
            <div class="text-gray-600 text-sm font-medium">Valor Total Inventario</div>
            <div class="mt-3 flex items-center gap-2 text-xs">
              <span class="text-green-600 font-semibold">↑ 8%</span>
              <span class="text-gray-500">vs mes anterior</span>
            </div>
          </div>

          <!-- Productos en Stock -->
          <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between mb-4">
              <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
              </div>
              <span class="text-xs text-gray-500">892 unidades</span>
            </div>
            <div class="text-2xl font-bold text-gray-900 mb-1">245 Productos</div>
            <div class="text-gray-600 text-sm font-medium">En Stock</div>
            <div class="mt-3 flex items-center gap-2 text-xs">
              <span class="text-orange-600 font-semibold">⚠️ 12</span>
              <span class="text-gray-500">productos bajo stock</span>
            </div>
          </div>

          <!-- Rotación Inventario -->
          <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-teal-500">
            <div class="flex items-center justify-between mb-4">
              <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
              </div>
              <span class="text-xs text-gray-500">Mensual</span>
            </div>
            <div class="text-2xl font-bold text-gray-900 mb-1">4.2x</div>
            <div class="text-gray-600 text-sm font-medium">Rotación de Inventario</div>
            <div class="mt-3 flex items-center gap-2 text-xs">
              <span class="text-green-600 font-semibold">Saludable</span>
              <span class="text-gray-500">promedio industria: 3.5x</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Sección: Órdenes de Servicio -->
      <div class="space-y-4">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
          </div>
          <h2 class="text-2xl font-bold text-gray-900">🔧 Órdenes de Servicio</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Pendientes -->
          <div class="bg-yellow-50 rounded-xl shadow-md p-6 border-2 border-yellow-300">
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-1">18</div>
            <div class="text-gray-700 text-sm font-medium">Pendientes</div>
          </div>

          <!-- En Proceso -->
          <div class="bg-blue-50 rounded-xl shadow-md p-6 border-2 border-blue-300">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-1">24</div>
            <div class="text-gray-700 text-sm font-medium">En Proceso</div>
          </div>

          <!-- Finalizadas -->
          <div class="bg-green-50 rounded-xl shadow-md p-6 border-2 border-green-300">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-1">156</div>
            <div class="text-gray-700 text-sm font-medium">Finalizadas (mes)</div>
          </div>

          <!-- Tiempo Promedio -->
          <div class="bg-purple-50 rounded-xl shadow-md p-6 border-2 border-purple-300">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
              </svg>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-1">3.2 días</div>
            <div class="text-gray-700 text-sm font-medium">Tiempo Promedio</div>
          </div>
        </div>
      </div>

      <!-- Sección: Técnicos y Comisiones -->
      <div class="space-y-4">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </div>
          <h2 class="text-2xl font-bold text-gray-900">👨‍🔧 Rendimiento de Técnicos</h2>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
          <div class="space-y-4">
            <!-- Técnico 1 -->
            <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-blue-50 to-transparent rounded-lg border border-blue-200">
              <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <span class="text-xl font-bold text-blue-600">JD</span>
              </div>
              <div class="flex-1">
                <div class="flex items-center justify-between mb-1">
                  <span class="font-semibold text-gray-900">Juan Díaz</span>
                  <span class="text-sm font-medium text-green-600">$1,240,000 comisiones</span>
                </div>
                <div class="flex items-center gap-4 text-sm text-gray-600">
                  <span>⚙️ 42 reparaciones</span>
                  <span>⭐ 4.8/5.0</span>
                  <span>⏱️ 2.8 días promedio</span>
                </div>
              </div>
            </div>

            <!-- Técnico 2 -->
            <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-purple-50 to-transparent rounded-lg border border-purple-200">
              <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <span class="text-xl font-bold text-purple-600">MR</span>
              </div>
              <div class="flex-1">
                <div class="flex items-center justify-between mb-1">
                  <span class="font-semibold text-gray-900">María Rodríguez</span>
                  <span class="text-sm font-medium text-green-600">$980,000 comisiones</span>
                </div>
                <div class="flex items-center gap-4 text-sm text-gray-600">
                  <span>⚙️ 38 reparaciones</span>
                  <span>⭐ 4.9/5.0</span>
                  <span>⏱️ 2.5 días promedio</span>
                </div>
              </div>
            </div>

            <!-- Técnico 3 -->
            <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-green-50 to-transparent rounded-lg border border-green-200">
              <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <span class="text-xl font-bold text-green-600">CP</span>
              </div>
              <div class="flex-1">
                <div class="flex items-center justify-between mb-1">
                  <span class="font-semibold text-gray-900">Carlos Pérez</span>
                  <span class="text-sm font-medium text-green-600">$1,150,000 comisiones</span>
                </div>
                <div class="flex items-center gap-4 text-sm text-gray-600">
                  <span>⚙️ 45 reparaciones</span>
                  <span>⭐ 4.7/5.0</span>
                  <span>⏱️ 3.1 días promedio</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sección: Gastos -->
      <div class="space-y-4">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
            </svg>
          </div>
          <h2 class="text-2xl font-bold text-gray-900">💸 Control de Gastos</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Compras Inventario -->
          <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center gap-3 mb-3">
              <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
              </div>
              <span class="text-sm font-medium text-gray-600">Compras</span>
            </div>
            <div class="text-2xl font-bold text-gray-900">$12,500,000</div>
            <div class="text-xs text-gray-500 mt-1">Inventario este mes</div>
          </div>

          <!-- Comisiones -->
          <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center gap-3 mb-3">
              <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <span class="text-sm font-medium text-gray-600">Comisiones</span>
            </div>
            <div class="text-2xl font-bold text-gray-900">$3,370,000</div>
            <div class="text-xs text-gray-500 mt-1">Técnicos este mes</div>
          </div>

          <!-- Operativos -->
          <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center gap-3 mb-3">
              <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
              <span class="text-sm font-medium text-gray-600">Operativos</span>
            </div>
            <div class="text-2xl font-bold text-gray-900">$1,850,000</div>
            <div class="text-xs text-gray-500 mt-1">Servicios + Arriendo</div>
          </div>

          <!-- Préstamos -->
          <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center gap-3 mb-3">
              <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
              </div>
              <span class="text-sm font-medium text-gray-600">Préstamos</span>
            </div>
            <div class="text-2xl font-bold text-gray-900">$2,400,000</div>
            <div class="text-xs text-gray-500 mt-1">Cuotas este mes</div>
          </div>
        </div>
      </div>

      <!-- Gráficos Comparativos -->
      <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-6">📈 Gráficos y Tendencias</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Placeholder Gráfico 1 -->
          <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <p class="text-gray-600 font-medium">Ingresos vs Gastos Mensuales</p>
            <p class="text-sm text-gray-500 mt-1">Gráfico de barras comparativo</p>
          </div>

          <!-- Placeholder Gráfico 2 -->
          <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
            </svg>
            <p class="text-gray-600 font-medium">Distribución de Ventas</p>
            <p class="text-sm text-gray-500 mt-1">Por categoría de producto</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

const periodos = [
  { id: 'hoy', label: 'Hoy' },
  { id: 'semana', label: 'Esta Semana' },
  { id: 'mes', label: 'Este Mes' },
  { id: 'año', label: 'Este Año' }
]

const periodoSeleccionado = ref('mes')

const fechaActual = computed(() => {
  return new Date().toLocaleDateString('es-CO', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
})
</script>

<style scoped>
/* Animaciones suaves */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.space-y-4 > * {
  animation: fadeIn 0.5s ease-out;
}
</style>
