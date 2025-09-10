export type EstadoOrden = 'pendiente' | 'recibida' | 'en_proceso' | 'finalizada' | 'cerrada'

export interface OrdenServicio {
  id: number
  codigo_orden: string
  cliente_id: number
  estado: EstadoOrden
  observaciones_generales?: string | null
  fecha_creacion: string
  fecha_cierre?: string | null
  created_at?: string
  updated_at?: string
}

// Payload para crear
export interface CreateOrdenPayload {
  estado: EstadoOrden
  observaciones_generales?: string | null
  fecha_creacion: string
  fecha_cierre?: string | null
}

// Payload para actualizar
export type UpdateOrdenPayload = Partial<CreateOrdenPayload>