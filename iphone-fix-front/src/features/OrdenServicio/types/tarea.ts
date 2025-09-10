// features/OrdenServicio/types/tarea.ts

export type Tarea = {
  id: number
  equipo_os_id: number
  tipo_trabajo_id: number
  costo_aplicado: number
  estado: 'pendiente' | 'en_proceso' | 'completada' | 'cancelada' | string
  observaciones?: string | null
  created_at?: string
  updated_at?: string
}

export interface CreateTareaPayload {
  tipo_trabajo_id: number
  observaciones?: string
  costo_aplicado: number | null   // 👈 agrega esto
}


export type UpdateTareaPayload = Partial<CreateTareaPayload> & {
  estado?: Tarea['estado']
}
