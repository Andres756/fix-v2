// features/OrdenServicio/types/equipo.ts

/** ===== Tipos alineados a la tabla equipos_orden_servicio ===== */
export interface Equipo {
  id: number
  orden_id: number
  imei_serial: string
  marca: string
  modelo: string
  descripcion_problema?: string
  contrasena_equipo?: string
  valor_estimado?: number | null
  fecha_estimada_entrega?: string
  tecnico_asignado?: number | null   // 👈 nuevo
  comision_habilitada: boolean
  tipo_comision?: 'porcentaje' | 'fijo' | null
  valor_comision?: number | null
  estado?: string
  observaciones?: string
  fecha_finalizacion?: string
  created_at?: string
  updated_at?: string
}

export interface CreateEquipoPayload {
  marca: string
  modelo: string
  imei_serial?: string
  descripcion_problema?: string
  contrasena_equipo?: string
  valor_estimado?: number | null
  fecha_estimada_entrega?: string
  tecnico_asignado?: number | null   // 👈 nuevo
  comision_habilitada: boolean
  tipo_comision?: 'porcentaje' | 'fijo' | null
  valor_comision?: number | null
}
