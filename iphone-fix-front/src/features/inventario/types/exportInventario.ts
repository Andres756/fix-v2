// src/features/inventario/types/exportInventario.ts
export interface InventarioExportFilters {
  tipo_inventario_id: string | number
  activo: string
  filtro_stock: string
  fecha_desde: string
  fecha_hasta: string
  tipo_inventario_nombre?: string
}

export interface TipoInventario {
  id: number
  nombre: string
}