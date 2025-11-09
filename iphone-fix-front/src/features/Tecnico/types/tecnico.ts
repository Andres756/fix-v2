export interface Tecnico {
  id: number
  nombre: string
  email?: string
}

export interface Tarea {
  id: number
  nombre: string
  estado: 'pendiente' | 'en_proceso' | 'completada' | 'cancelada'
  // Sin costo_aplicado para el frontend del técnico
}

export interface ResumenTareas {
  total: number
  pendientes: number
  en_proceso: number
  completadas: number
}

export interface Comision {
  habilitada: boolean
  tipo?: 'porcentaje' | 'fijo'
  valor?: number
  ganancia_estimada?: number
}

export interface EquipoAsignado {
  id: number
  orden_id: number
  cliente_id: number
  marca: string
  modelo: string
  estado: string
  imei_serial?: string
  fecha_estimada_entrega?: string
  comision?: {
    habilitada: boolean
    tipo: string
    valor: number
    ganancia_estimada: number
  }
  resumen_tareas: {
    total: number
    pendientes: number
    en_proceso: number
    completadas: number
  }
  tareas?: any[]

  // ✅ Campos adicionales que usa el template
  cliente?: string
  orden_codigo?: string
}

export interface DashboardStats {
  total_equipos: number
  equipos_pendientes: number
  equipos_completados: number
  equipos_en_proceso: number
  
  total_tareas: number
  tareas_pendientes: number
  tareas_en_proceso: number
  tareas_completadas: number
}

export interface DashboardData {
  stats: DashboardStats
  equipos: EquipoAsignado[]
}

export interface GananciasEquipo {
  equipo_os_id: number
  marca: string
  modelo: string
  total_tareas_valor: number
  comision: {
    tipo: 'porcentaje' | 'fijo'
    valor: number
    ganancia: number
  }
}

export interface GananciasTecnico {
  total_ganado: number
  equipos_con_comision: number
  equipos: GananciasEquipo[]
}

export interface HistorialTarea {
  id: number
  estado_anterior: string | null
  estado_nuevo: string
  cambiado_en: string
  tecnico: {
    id: number
    nombre: string
  }
}

