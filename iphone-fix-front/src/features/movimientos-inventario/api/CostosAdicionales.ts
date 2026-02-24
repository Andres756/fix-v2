// src/features/movimientos-inventario/api/costosAdicionales.ts

import http from '../../../shared/api/http'

// ============================================
// TIPOS
// ============================================

export interface RepuestoInventario {
  id: number
  entrada_id: number
  inventario_id: number
  cantidad: number
  costo_unitario: number
  costo_total: number
  observaciones?: string
  created_at: string
  updated_at: string
  inventario_nombre?: string
  inventario_codigo?: string
}

export interface RepuestoExterno {
  id: number
  entrada_id: number
  proveedor_id?: number
  descripcion: string
  cantidad: number
  costo_unitario: number
  costo_total: number
  observaciones?: string
  created_at: string
  updated_at: string
  proveedor_nombre?: string
}

export interface PagoTecnico {
  id: number
  entrada_id: number
  tecnico_id?: number
  descripcion: string
  tipo_pago: 'fijo' | 'porcentaje'
  valor: number
  costo_calculado: number
  observaciones?: string
  created_at: string
  updated_at: string
  tecnico_nombre?: string
}

export interface CostosAdicionales {
  repuestos_inventario: RepuestoInventario[]
  repuestos_externos: RepuestoExterno[]
  pagos_tecnicos: PagoTecnico[]
}

export interface ResumenCostos {
  costo_base: number
  repuestos_inventario: number
  repuestos_externos: number
  total_repuestos: number
  total_tecnicos: number
  costo_total_final: number
}

// ============================================
// PAYLOADS
// ============================================

export interface CreateRepuestoInventarioPayload {
  inventario_id: number
  cantidad: number
  observaciones?: string
}

export interface CreateRepuestoExternoPayload {
  descripcion: string
  cantidad: number
  costo_unitario: number
  proveedor_id?: number
  observaciones?: string
  a_credito?: boolean  // NUEVO
  metodo_pago_id?: number  // NUEVO
}

export interface CreatePagoTecnicoPayload {
  descripcion: string
  tipo_pago: 'fijo' | 'porcentaje'
  valor: number
  tecnico_id?: number
  observaciones?: string
}

// ============================================
// API FUNCTIONS
// ============================================

/**
 * Obtener todos los costos adicionales de una entrada
 */
export async function fetchCostosAdicionales(entradaId: number): Promise<CostosAdicionales> {
  const { data } = await http.get(`/inventario/entradas/${entradaId}/costos-adicionales`)
  return data.data
}

/**
 * Obtener resumen de costos
 */
export async function fetchResumenCostos(entradaId: number): Promise<ResumenCostos> {
  const { data } = await http.get(`/inventario/entradas/${entradaId}/costos-adicionales/resumen`)
  return data.data
}

/**
 * Agregar repuesto de inventario
 */
export async function addRepuestoInventario(
  entradaId: number,
  payload: CreateRepuestoInventarioPayload
): Promise<RepuestoInventario> {
  const { data } = await http.post(
    `/inventario/entradas/${entradaId}/costos-adicionales/repuestos-inventario`,
    payload
  )
  return data.data
}

/**
 * Agregar repuesto externo
 */
export async function addRepuestoExterno(
  entradaId: number,
  payload: CreateRepuestoExternoPayload
): Promise<RepuestoExterno> {
  const { data } = await http.post(
    `/inventario/entradas/${entradaId}/costos-adicionales/repuestos-externos`,
    payload
  )
  return data.data
}

/**
 * Agregar pago a técnico
 */
export async function addPagoTecnico(
  entradaId: number,
  payload: CreatePagoTecnicoPayload
): Promise<PagoTecnico> {
  const { data } = await http.post(
    `/inventario/entradas/${entradaId}/costos-adicionales/pagos-tecnicos`,
    payload
  )
  return data.data
}

/**
 * Eliminar un costo adicional
 */
export async function deleteCostoAdicional(
  entradaId: number,
  tipo: 'repuesto-inventario' | 'repuesto-externo' | 'pago-tecnico',
  id: number
): Promise<void> {
  await http.delete(`/inventario/entradas/${entradaId}/costos-adicionales/${tipo}/${id}`)
}