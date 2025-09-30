// iphone-fix-front/src/features/inventario/api/options.ts
import axios from 'axios'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000'

export interface Lote {
  id: number
  numero_lote: string
  fecha_ingreso?: string
  proveedor_id?: number
  proveedor?: {
    id: number
    nombre: string
  }
}

/**
 * Obtiene todos los lotes disponibles
 */
export const fetchLotesOptions = async (): Promise<Lote[]> => {
  const response = await axios.get(`${API_URL}/api/inventario/lotes`)
  return response.data.data || response.data
}

/**
 * Crea un nuevo lote
 */
export const createLote = async (data: { 
  numero_lote: string
  fecha_ingreso?: string
  proveedor_id?: number 
}) => {
  const response = await axios.post(`${API_URL}/api/inventario/lotes`, data)
  return response.data
}