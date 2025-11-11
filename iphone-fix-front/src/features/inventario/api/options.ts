// ✅ src/features/inventario/api/options.ts
import axios from 'axios'

// ✅ Usa la variable de entorno global (definida en .env)
const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL, // Ej: https://api.coruni.shop/api
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

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
 * 📦 Obtiene todos los lotes disponibles
 */
export const fetchLotesOptions = async (): Promise<Lote[]> => {
  const response = await api.get('/inventario/lotes')
  return response.data.data || response.data
}

/**
 * ➕ Crea un nuevo lote
 */
export const createLote = async (data: {
  numero_lote: string
  fecha_ingreso?: string
  proveedor_id?: number
}) => {
  const response = await api.post('/inventario/lotes', data)
  return response.data
}
