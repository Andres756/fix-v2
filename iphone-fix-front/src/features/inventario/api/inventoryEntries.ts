// iphone-fix-front/src/features/inventario/api/inventoryEntries.ts
import axios from 'axios'
import type { CreateEntryPayload } from '../types/inventoryEntry'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000'

/**
 * Crea una nueva entrada de inventario
 */
export const createEntrada = async (payload: CreateEntryPayload) => {
  const response = await axios.post(`${API_URL}/api/entradas`, payload)
  return response.data
}

/**
 * Obtiene todas las entradas de inventario
 */
export const fetchEntradas = async () => {
  const response = await axios.get(`${API_URL}/api/entradas`)
  return response.data.data
}

/**
 * Obtiene una entrada específica por ID
 */
export const fetchEntrada = async (id: number) => {
  const response = await axios.get(`${API_URL}/api/entradas/${id}`)
  return response.data.data
}

/**
 * Elimina una entrada de inventario
 */
export const deleteEntrada = async (id: number) => {
  const response = await axios.delete(`${API_URL}/api/entradas/${id}`)
  return response.data
}