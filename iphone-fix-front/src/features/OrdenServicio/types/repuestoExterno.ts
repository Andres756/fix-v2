// types/repuestoExterno.ts
export interface RepuestoExterno {
  id: number
  equipo_os_id: number
  descripcion: string
  cantidad: number
  costo_unitario: string
  costo_total: string
  proveedor_id?: number | null
  proveedor?: {
    id: number
    nombre: string
    contacto_nombre?: string
    telefono?: string
    correo?: string
  } | null
  observaciones?: string | null
  fecha_gasto?: string | null
  created_at: string
  updated_at: string
}

export interface CreateRepuestoExternoPayload {
  descripcion: string
  cantidad: number
  costo_unitario: number
  proveedor_id?: number | null
  observaciones?: string | null
  fecha_gasto?: string | null
}
