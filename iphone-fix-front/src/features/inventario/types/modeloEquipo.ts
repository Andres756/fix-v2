// src/features/inventario/types/modeloEquipo.ts

export interface ModeloEquipo {
  id: number
  nombre: string
  marca: string
  familia?: string | null
  anio_lanzamiento?: number | null
  descripcion?: string | null
  activo: boolean
  created_at?: string
  updated_at?: string
}

export interface ModeloEquipoOption {
  id: number
  nombre: string
  marca: string
  familia?: string | null
}

export interface ReporteModeloInventario {
  id: number
  nombre: string
  marca: string
  familia?: string | null
  cantidad_disponible: number
  variantes_almacenamiento: number
  variantes_color: number
  costo_minimo: number
  costo_maximo: number
  costo_promedio: number
  precio_minimo: number
  precio_maximo: number
  valor_inventario: number
}