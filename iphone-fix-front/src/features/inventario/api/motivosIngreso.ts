// iphone-fix-front/src/features/inventario/api/motivosIngreso.ts
import axios from 'axios'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000'

export interface MotivoIngreso {
  id: number
  nombre: string
  descripcion?: string
  activo?: boolean
}

/**
 * Obtiene todos los motivos de ingreso
 */
export const fetchMotivosIngreso = async (): Promise<MotivoIngreso[]> => {
  const response = await axios.get(`${API_URL}/api/parametros/motivos-ingreso`)
  return response.data.data || response.data
}

/**
 * Crea un nuevo motivo de ingreso
 */
export const createMotivoIngreso = async (data: { nombre: string; descripcion?: string }) => {
  const response = await axios.post(`${API_URL}/api/parametros/motivos-ingreso`, data)
  return response.data
}

/**
 * Actualiza un motivo de ingreso
 */
export const updateMotivoIngreso = async (id: number, data: { nombre: string; descripcion?: string }) => {
  const response = await axios.put(`${API_URL}/api/parametros/motivos-ingreso/${id}`, data)
  return response.data
}

/**
 * Elimina un motivo de ingreso
 */
export const deleteMotivoIngreso = async (id: number) => {
  const response = await axios.delete(`${API_URL}/api/parametros/motivos-ingreso/${id}`)
  return response.data
}