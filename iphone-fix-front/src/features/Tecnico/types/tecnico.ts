export interface Tarea {
  id: number
  nombre: string
  costo_aplicado: number
  estado: 'pendiente' | 'en_proceso' | 'completada' | 'cancelada'
}

export interface EquipoAsignado {
  id: number
  marca: string
  modelo: string
  imei_serial: string
  estado: string
  tareas: Tarea[]
  comision: {
    tipo: 'porcentaje' | 'fijo' | null
    valor: number | null
    ganancia: number
  }
}

export interface GananciasEquipo {
  equipo_id: number
  modelo: string
  total_tareas: number
  comision: {
    tipo: 'porcentaje' | 'fijo' | null
    valor: number | null
    ganancia: number
  }
}

export interface GananciasTecnico {
  total_ganado: number
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

export interface Tecnico {
  id: number
  nombre: string
  email?: string
}

