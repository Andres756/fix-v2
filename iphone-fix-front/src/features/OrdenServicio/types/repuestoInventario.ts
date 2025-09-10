export interface RepuestoInventario {
  id: number
  equipo_os_id: number
  inventario_id: number
  nombre: string
  codigo: string
  cantidad: number
  costo_unitario: number
  costo_total: number
  created_at?: string
  updated_at?: string
}

export interface CreateRepuestoInventarioPayload {
  inventario_id: number
  cantidad: number
}
