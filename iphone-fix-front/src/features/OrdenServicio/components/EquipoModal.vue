<template>
  <Teleport to="body">
    <transition
      name="modal"
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
      appear
    >
      <div v-if="open" class="fixed inset-0 z-[9999] overflow-y-auto">
        <!-- Backdrop -->
        <div
          class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
          @click="emit('close')"
        ></div>

        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
          <div class="relative w-full max-w-5xl bg-white rounded-2xl shadow-2xl">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-gray-900">Gestión de Equipos</h3>
                  <p class="text-sm text-gray-500">Orden de Servicio #{{ String(ordenId).padStart(4, '0') }}</p>
                </div>
              </div>
              <button
                @click="emit('close')"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-6 max-h-[80vh] overflow-y-auto">
              <!-- Acciones -->
              <div class="flex items-center justify-between bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4">
                <div class="flex items-center space-x-3">
                  <div class="p-2 bg-violet-100 rounded-lg">
                    <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                  </div>
                  <div>
                    <h4 class="text-lg font-semibold text-gray-800">Equipos Registrados</h4>
                    <p class="text-sm text-gray-600">{{ equipos.length }} equipo{{ equipos.length !== 1 ? 's' : '' }} en total</p>
                  </div>
                </div>
                <button 
                  @click="startCreate" 
                  class="px-6 py-2.5 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition-colors font-medium flex items-center gap-2"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                  </svg>
                  Agregar Equipo
                </button>
              </div>

              <!-- Lista de equipos -->
              <div class="space-y-3">
                <div v-if="isLoading" class="flex items-center justify-center py-12">
                  <div class="flex items-center space-x-3">
                    <div class="animate-spin rounded-full h-6 w-6 border-2 border-violet-600 border-t-transparent"></div>
                    <span class="text-gray-600">Cargando equipos...</span>
                  </div>
                </div>
                
                <div v-else-if="equipos.length === 0" class="text-center py-12">
                  <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                  </div>
                  <h3 class="text-lg font-medium text-gray-900 mb-2">No hay equipos registrados</h3>
                  <p class="text-gray-600">Comienza agregando tu primer equipo a esta orden de servicio</p>
                </div>

                <div v-else class="space-y-2">
                  <div 
                    v-for="eq in equipos" 
                    :key="eq.id" 
                    class="bg-white border border-gray-200 rounded-lg p-3 hover:shadow-md hover:border-violet-200 transition-all duration-200"
                  >
                    <div class="flex items-center justify-between">
                      <div class="flex items-center space-x-3 flex-1">
                        <div class="p-1.5 bg-gradient-to-br from-violet-50 to-purple-50 rounded-lg">
                          <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                          </svg>
                        </div>
                        
                        <div class="flex-1 min-w-0">
                          <div class="flex items-center justify-between mb-1">
                            <h5 class="font-semibold text-gray-900 text-base truncate">
                              {{ eq.marca ? (eq.marca + ' ') : '' }}{{ eq.modelo }}
                            </h5>
                            <div class="flex items-center space-x-2 ml-3">
                              <button 
                                @click="startEdit(eq)" 
                                class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                title="Editar equipo"
                              >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                              </button>
                              <button 
                                @click="openTasks(eq)" 
                                class="px-2.5 py-1.5 bg-violet-50 text-violet-700 rounded-lg hover:bg-violet-100 transition-colors text-xs font-medium flex items-center gap-1.5"
                              >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Tareas
                              </button>
                              <button 
                                @click="openRepuestos(eq)" 
                                class="px-2.5 py-1.5 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors text-xs font-medium flex items-center gap-1.5"
                              >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h4a1 1 0 011 1v2M7 4h6M7 4v12a1 1 0 001 1h4a1 1 0 001-1V4M5 7h10v2H5V7z"/>
                                </svg>
                                Repuestos
                              </button>
                              <!-- ✅ Nuevo tab de repuestos externos -->
                              <button
                                @click="inlineTab = 'repuestosExternos'"
                                :class="inlineTab === 'repuestosExternos' ? 'bg-white shadow text-orange-700' : 'text-gray-600 hover:text-gray-900'"
                                class="flex-1 px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center justify-center gap-2"
                              >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .843-3 1.882v8.236c0 1.039 1.343 1.882 3 1.882s3-.843 3-1.882V9.882C15 8.843 13.657 8 12 8z"/>
                                </svg>
                                Repuestos Externos
                              </button>
                            </div>
                          </div>
                          
                          <!-- Info compacta en una sola línea -->
                          <div class="flex items-center space-x-4 text-xs text-gray-500">
                            <span v-if="eq.imei_serial" class="flex items-center space-x-1">
                              <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                              </svg>
                              <span>{{ eq.imei_serial }}</span>
                            </span>

                            <span v-if="eq.fecha_estimada_entrega" class="flex items-center space-x-1">
                              <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                              </svg>
                              <span>{{ formatDate(eq.fecha_estimada_entrega) }}</span>
                            </span>

                            <span v-if="eq.comision_habilitada" class="flex items-center space-x-1">
                              <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 5.5C14.8 4.1 13.6 3 12.1 3C10.6 3 9.4 4.1 9.2 5.5L3 7V9L9.5 7.5C9.6 7.5 9.7 7.6 9.7 7.6L12 8.5C12.3 8.6 12.7 8.6 13 8.5L15.3 7.6C15.3 7.6 15.4 7.5 15.5 7.5L21 9ZM12 10.5C10.1 10.5 8.5 11.94 8.5 13.75V16C8.5 16.8 9.2 17.5 10 17.5H14C14.8 17.5 15.5 16.8 15.5 16V13.75C15.5 11.94 13.9 10.5 12 10.5ZM12 19C8.13 19 5 16.5 5 13.5V11.81C3.84 12.88 3 14.34 3 16C3 19.31 6.69 22 12 22C17.31 22 21 19.31 21 16C21 14.34 20.16 12.88 19 11.81V13.5C19 16.5 15.87 19 12 19Z"/>
                              </svg>
                              <strong class="text-yellow-700">
                                {{ eq.tipo_comision === 'porcentaje' ? eq.valor_comision + '%' : number(eq.valor_comision) }}
                              </strong>
                            </span>
                          </div>
                          
                          <!-- Descripción del problema si existe -->
                          <p v-if="eq.descripcion_problema" class="text-xs text-gray-600 mt-1 truncate">
                            {{ eq.descripcion_problema }}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Formulario (crear/editar) -->
              <div v-if="showForm" class="border-t pt-6">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                  <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                      <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                      </div>
                      <div>
                        <h4 class="text-lg font-semibold text-gray-800">
                          {{ editingId ? 'Editar Equipo' : 'Nuevo Equipo' }}
                        </h4>
                        <p class="text-sm text-gray-600">Complete la información del equipo</p>
                      </div>
                    </div>
                    <button 
                      @click="cancelForm" 
                      class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-white/50 transition-colors"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                      </svg>
                    </button>
                  </div>

                  <form @submit.prevent="guardar" class="space-y-5">
                    <!-- Información básica -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                      <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Marca</label>
                        <div class="relative">
                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                          </div>
                          <input
                            v-model.trim="form.marca"
                            type="text"
                            placeholder="Apple, Samsung, Xiaomi..."
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                          />
                        </div>
                      </div>

                      <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Modelo *</label>
                        <div class="relative">
                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                          </div>
                          <input
                            v-model.trim="form.modelo"
                            type="text"
                            placeholder="iPhone 13 Pro, Galaxy S21..."
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            required
                          />
                        </div>
                      </div>

                      <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">IMEI / Serial</label>
                        <div class="relative">
                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                            </svg>
                          </div>
                          <input
                            v-model.trim="form.imei_serial"
                            type="text"
                            placeholder="Número IMEI o Serial"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                          />
                        </div>
                      </div>

                      <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Fecha estimada de entrega</label>
                        <div class="relative">
                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                          </div>
                          <input
                            v-model="form.fecha_estimada_entrega"
                            type="date"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                          />
                        </div>
                      </div>
                    </div>

                    <!-- Descripción -->
                    <div class="space-y-2">
                      <label class="block text-sm font-semibold text-gray-700">Descripción del problema</label>
                      <textarea 
                        v-model.trim="form.descripcion_problema" 
                        rows="3" 
                        placeholder="Describe detalladamente el problema o falla del equipo..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"
                      />
                    </div>

                    <!-- Información adicional -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                      <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Contraseña del equipo</label>
                        <div class="relative">
                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                          </div>
                          <input
                            v-model.trim="form.contrasena_equipo"
                            type="text"
                            placeholder="PIN, patrón, contraseña..."
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                          />
                        </div>
                      </div>

                      <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Valor estimado</label>
                        <div class="relative">
                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500">$</span>
                          </div>
                          <input
                            v-model.number="form.valor_estimado"
                            type="number"
                            min="0"
                            step="1000"
                            placeholder="0"
                            class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                          />
                        </div>
                      </div>
                    </div>

                    <!-- Técnico asignado -->
                    <div class="space-y-2">
                      <label class="block text-sm font-semibold text-gray-700">Técnico asignado</label>
                      <select v-model="form.tecnico_asignado" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option :value="null">— Sin asignar —</option>
                        <option v-for="t in tecnicos" :key="t.id" :value="t.id">
                          {{ t.nombre }}
                        </option>
                      </select>
                    </div>

                    <!-- Comisión -->
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                      <div class="flex items-center space-x-3 mb-4">
                        <input 
                          id="comision" 
                          type="checkbox" 
                          v-model="form.comision_habilitada" 
                          class="h-4 w-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500"
                        />
                        <label for="comision" class="text-sm font-semibold text-gray-700">Habilitar comisión para este equipo</label>
                      </div>
                      <div v-if="form.comision_habilitada" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-2">
                          <label class="block text-sm font-semibold text-gray-700">Tipo de comisión</label>
                          <select v-model="form.tipo_comision" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
                            <option value="porcentaje">Porcentaje (%)</option>
                            <option value="fijo">Valor fijo</option>
                          </select>
                        </div>
                        <div class="space-y-2">
                          <label class="block text-sm font-semibold text-gray-700">
                            {{ form.tipo_comision === 'porcentaje' ? 'Porcentaje (0—100)' : 'Valor en COP' }}
                          </label>
                          <div class="relative">
                            <div v-if="form.tipo_comision !== 'porcentaje'" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                              <span class="text-gray-500">$</span>
                            </div>
                            <input 
                              v-model.number="form.valor_comision" 
                              :min="form.tipo_comision === 'porcentaje' ? 0 : 0" 
                              :max="form.tipo_comision === 'porcentaje' ? 100 : undefined" 
                              type="number" 
                              step="0.01" 
                              :class="form.tipo_comision === 'porcentaje' ? 'w-full px-4 py-3' : 'w-full pl-8 pr-4 py-3'"
                              class="border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                              :placeholder="form.tipo_comision === 'porcentaje' ? 'Ej: 10' : '50000'" 
                              required 
                            />
                            <div v-if="form.tipo_comision === 'porcentaje'" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                              <span class="text-gray-500">%</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end gap-3 pt-4">
                      <button
                        type="button"
                        @click="cancelForm"
                        class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                      >
                        Cancelar
                      </button>
                      <button
                        type="submit"
                        :disabled="isSaving"
                        class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center gap-2 disabled:opacity-50"
                      >
                        <svg v-if="isSaving" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                          <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/>
                          <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" class="opacity-75"/>
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ isSaving ? 'Guardando...' : (editingId ? 'Actualizar' : 'Crear Equipo') }}
                      </button>
                    </div>
                  </form>
                </div>
              </div>

              <!-- Panel de tareas/repuestos + Resumen -->
              <div v-if="inlineEquipoId && !showForm" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                  <!-- Tabs para cambiar entre tareas y repuestos -->
                  <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg mb-4">
                    <button
                      @click="inlineTab = 'tareas'"
                      :class="inlineTab === 'tareas' ? 'bg-white shadow text-violet-700' : 'text-gray-600 hover:text-gray-900'"
                      class="flex-1 px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center justify-center gap-2"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                      </svg>
                      Tareas
                    </button>
                    <button
                      @click="inlineTab = 'repuestos'"
                      :class="inlineTab === 'repuestos' ? 'bg-white shadow text-green-700' : 'text-gray-600 hover:text-gray-900'"
                      class="flex-1 px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center justify-center gap-2"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h4a1 1 0 011 1v2M7 4h6M7 4v12a1 1 0 001 1h4a1 1 0 001-1V4M5 7h10v2H5V7z"/>
                      </svg>
                      Repuestos
                    </button>
                  </div>

                  <!-- Contenido del tab activo -->
                  <TareasEquipoInline
                    v-if="inlineTab === 'tareas'"
                    :clienteId="clienteId"
                    :ordenId="ordenId"
                    :equipoId="inlineEquipoId"
                    :equipoImei="inlineEquipoImei"
                    @changed="emit('updated')"
                    @total-changed="t => (totalTareas = t)"
                  />
                  
                  <RepuestosInventarioInline
                    v-if="inlineTab === 'repuestos'"
                    :clienteId="clienteId"
                    :ordenId="ordenId"
                    :equipoId="inlineEquipoId"
                    @changed="handleRepuestosChanged"
                  />

                  <!-- ✅ Nuevo componente inline de repuestos externos -->
                  <RepuestosExternosInline
                    v-if="inlineTab === 'repuestosExternos'"
                    :clienteId="clienteId"
                    :ordenId="ordenId"
                    :equipoId="inlineEquipoId"
                    @changed="handleRepuestosExternosChanged"
                    @total-changed="t => (totalRepuestosExternos = t)"
                  />
                </div>
                
                <div class="lg:col-span-1">
                  <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-5 border border-green-200 sticky top-4">
                    <div class="flex items-center space-x-3 mb-4">
                      <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                      </div>
                      <h5 class="font-semibold text-green-800">Resumen Financiero</h5>
                    </div>
                    <div class="space-y-3">
                      <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">Total tareas:</span>
                        <span class="font-semibold text-green-700">{{ money(totalTareas) }}</span>
                      </div>
                      <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">Total repuestos:</span>
                        <span class="font-semibold text-green-700">{{ money(totalRepuestos) }}</span>
                      </div>
                      <!-- ✅ Nuevo total repuestos externos -->
                      <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">Total repuestos externos:</span>
                        <span class="font-semibold text-green-700">{{ money(totalRepuestosExternos) }}</span>
                      </div>
                      <div v-if="currentEquipoValor" class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">Valor equipo:</span>
                        <span class="font-semibold text-green-700">{{ money(currentEquipoValor) }}</span>
                      </div>
                      <div class="border-t border-green-200 pt-3 flex justify-between items-center">
                        <span class="font-semibold text-green-800">Total aproximado:</span>
                        <span class="text-lg font-bold text-green-800">{{ money(totalGeneral) }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t flex justify-end">
              <button 
                @click="emit('close')" 
                class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
              >
                Cerrar
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, watch, computed, onMounted } from 'vue'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

// APIs de equipos
import { fetchEquipos, createEquipo, updateEquipo } from '../api/equipos'
import type { CreateEquipoPayload, Equipo } from '../types/equipo'

// Componentes inline
import TareasEquipoInline from './TareasEquipoInline.vue'
import RepuestosInventarioInline from './RepuestosInventarioInline.vue'
import RepuestosExternosInline from './RepuestosExternosInline.vue' // ✅ Nuevo

// API técnicos
import { fetchTecnicos, type Tecnico } from '../api/tecnicos'

// Props y emits del modal
const props = defineProps<{ open: boolean; clienteId: number; ordenId: number }>()
const emit = defineEmits<{ (e: 'close'): void; (e: 'created'): void; (e: 'updated'): void }>()

// ===== Estado principal =====
const inlineEquipoId = ref<number | null>(null)
// Ahora tenemos 3 tabs disponibles
const inlineTab = ref<'tareas' | 'repuestos' | 'repuestosExternos'>('tareas')

// Lista de equipos
type EquipoItem = Equipo
const equipos = ref<EquipoItem[]>([])
const isLoading = ref(false)
const showForm = ref(false)
const isSaving = ref(false)
const editingId = ref<number | null>(null)

// Totales para el resumen
const totalTareas = ref(0)
const totalRepuestos = ref(0)
const totalRepuestosExternos = ref(0) // ✅ Nuevo total

// Helpers de equipo actual
const inlineEquipoImei = computed(() => equipos.value.find(e => e.id === inlineEquipoId.value)?.imei_serial || '')
const currentEquipoValor = computed(() => {
  if (!inlineEquipoId.value) return 0
  const equipo = equipos.value.find(e => e.id === inlineEquipoId.value)
  return Number(equipo?.valor_estimado || 0)
})
// ✅ Total general ahora incluye externos
const totalGeneral = computed(() =>
  totalTareas.value + totalRepuestos.value + totalRepuestosExternos.value + currentEquipoValor.value
)

// ===== Técnicos =====
const tecnicos = ref<Tecnico[]>([])
onMounted(async () => {
  try {
    tecnicos.value = await fetchTecnicos()
  } catch (e) {
    toast.error('No se pudieron cargar técnicos')
  }
})

// ===== Formulario =====
const form = ref<CreateEquipoPayload>({
  marca: '', modelo: '', imei_serial: '',
  descripcion_problema: '', contrasena_equipo: '',
  valor_estimado: null, fecha_estimada_entrega: '',
  tecnico_asignado: null,
  comision_habilitada: false, tipo_comision: 'porcentaje', valor_comision: null
})

// ===== Cargar equipos =====
async function loadEquipos() {
  try {
    isLoading.value = true
    equipos.value = await fetchEquipos(props.clienteId, props.ordenId)
  } catch (err: any) {
    toast.error(err?.response?.data?.message || 'No se pudieron cargar los equipos.')
  } finally {
    isLoading.value = false
  }
}
watch(() => [props.open, props.clienteId, props.ordenId], ([open]) => {
  if (open) {
    inlineEquipoId.value = null
    inlineTab.value = 'tareas'
    totalTareas.value = 0
    totalRepuestos.value = 0
    totalRepuestosExternos.value = 0 // ✅ reiniciamos
    loadEquipos()
    showForm.value = false
  }
})

// ===== UI Actions =====
function startCreate() {
  inlineEquipoId.value = null
  resetForm()
  showForm.value = true
}

function openTasks(eq: EquipoItem) {
  inlineEquipoId.value = eq.id
  inlineTab.value = 'tareas'
  totalTareas.value = 0
  totalRepuestos.value = 0
  totalRepuestosExternos.value = 0
  showForm.value = false
}

function openRepuestos(eq: EquipoItem) {
  inlineEquipoId.value = eq.id
  inlineTab.value = 'repuestos'
  totalTareas.value = 0
  totalRepuestos.value = 0
  totalRepuestosExternos.value = 0
  showForm.value = false
}

function openRepuestosExternos(eq: EquipoItem) { // ✅ Nuevo
  inlineEquipoId.value = eq.id
  inlineTab.value = 'repuestosExternos'
  totalTareas.value = 0
  totalRepuestos.value = 0
  totalRepuestosExternos.value = 0
  showForm.value = false
}

function startEdit(eq: EquipoItem) {
  editingId.value = eq.id
  form.value = {
    marca: eq.marca || '', modelo: eq.modelo || '', imei_serial: eq.imei_serial || '',
    descripcion_problema: eq.descripcion_problema || '', contrasena_equipo: eq.contrasena_equipo || '',
    valor_estimado: eq.valor_estimado ?? null, fecha_estimada_entrega: eq.fecha_estimada_entrega || '',
    tecnico_asignado: eq.tecnico_asignado ?? null,
    comision_habilitada: !!eq.comision_habilitada,
    tipo_comision: (eq.tipo_comision as any) ?? 'porcentaje',
    valor_comision: eq.valor_comision ?? null
  }
  showForm.value = true
}

function cancelForm() { showForm.value = false; resetForm() }

function resetForm() {
  form.value = {
    marca: '', modelo: '', imei_serial: '', descripcion_problema: '', contrasena_equipo: '',
    valor_estimado: null, fecha_estimada_entrega: '', tecnico_asignado: null,
    comision_habilitada: false, tipo_comision: 'porcentaje', valor_comision: null
  }
  editingId.value = null
}

// ===== Handlers =====
function handleRepuestosChanged() { emit('updated') }
function handleRepuestosExternosChanged() { emit('updated') } // ✅ Nuevo

// ===== Guardar equipo =====
async function guardar() {
  if (!form.value.comision_habilitada) {
    form.value.tipo_comision = null
    form.value.valor_comision = null
  } else {
    if (!form.value.tipo_comision) return toast.warning('Selecciona el tipo de comisión.')
    if (form.value.valor_comision == null) return toast.warning('Ingresa el valor de la comisión.')
    if (form.value.tipo_comision === 'porcentaje' &&
        (form.value.valor_comision < 0 || form.value.valor_comision > 100))
      return toast.warning('El porcentaje debe estar entre 0 y 100.')
  }

  try {
    isSaving.value = true
    if (editingId.value) {
      await updateEquipo(props.clienteId, props.ordenId, editingId.value, form.value)
      toast.success('¡Equipo actualizado exitosamente! 🎉')
      emit('updated')
      await loadEquipos()
      cancelForm()
    } else {
      const nuevo = await createEquipo(props.clienteId, props.ordenId, form.value)
      if (!nuevo?.id) throw new Error('Respuesta inesperada')
      toast.success('¡Equipo agregado exitosamente! 🎉')
      emit('created')
      await loadEquipos()
      cancelForm()
      inlineEquipoId.value = nuevo.id
      inlineTab.value = 'tareas'
      totalTareas.value = 0
      totalRepuestos.value = 0
      totalRepuestosExternos.value = 0
    }
  } catch (err: any) {
    toast.error(err?.response?.data?.message || 'No se pudo guardar el equipo.')
  } finally { isSaving.value = false }
}

// ===== Utils =====
const formatDate = (date?: string | null) =>
  !date ? '—' : new Date(date).toLocaleDateString('es-CO',
    { year: 'numeric', month: 'short', day: 'numeric' })

const number = (n?: number | null) =>
  (n == null ? '0.00' : n.toLocaleString('es-CO', { minimumFractionDigits: 2 }))

const money = (n: number) =>
  n.toLocaleString('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 })
</script>