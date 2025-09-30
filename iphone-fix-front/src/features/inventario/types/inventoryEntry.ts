// iphone-fix-front/src/features/inventario/types/inventoryEntry.ts

/**
 * Item individual de una entrada de inventario
 */
export interface InventoryEntryItem {
  inventario_id: number | string
  cantidad: number
  costo_unitario: number
}

/**
 * Formulario de entrada de inventario (para el frontend)
 */
export interface InventoryEntryForm {
  motivo_ingreso_id: number | string
  lote_id: number | string
  fecha_entrada: string
  observaciones: string
  items: InventoryEntryItem[]
}

/**
 * Payload para crear una entrada (lo que se envía al backend)
 */
export interface CreateEntryPayload {
  motivo_ingreso_id: number
  lote_id: number
  fecha_entrada: string
  observaciones: string | null
  items: {
    inventario_id: number
    cantidad: number
    costo_unitario: number
  }[]
}

/**
 * Entrada de inventario completa (respuesta del backend)
 */
export interface EntradaInventario {
  id: number
  motivo_ingreso_id: number
  lote_id: number
  fecha_entrada: string
  observaciones: string | null
  created_at: string
  updated_at: string
  
  // Relaciones
  motivo?: {
    id: number
    nombre: string
  }
  
  lote?: {
    id: number
    numero_lote: string
    proveedor?: {
      id: number
      nombre: string
    }
  }
  
  items?: EntradaItem[]
}

/**
 * Item de entrada con información completa
 */
export interface EntradaItem {
  id: number
  entrada_id: number
  inventario_id: number
  cantidad: number
  costo_unitario: number
  
  // Relación con inventario
  inventario?: {
    id: number
    nombre: string
    codigo: string
    stock: number
  }
}