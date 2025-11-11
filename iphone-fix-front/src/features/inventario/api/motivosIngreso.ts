// ✅ src/features/inventario/api/motivosIngreso.ts
import axios from 'axios'

// ✅ Usa la variable del .env (VITE_API_BASE_URL)
const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL, //
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

export interface MotivoIngreso {
  id: number
  nombre: string
  descripcion?: string
  activo?: boolean
}

/**
 * 📦 Obtiene todos los motivos de ingreso
 */
export const fetchMotivosIngreso = async (): Promise<MotivoIngreso[]> => {
  const response = await api.get('/parametros/motivos-ingreso')
  return response.data.data || response.data
}

/**
 * ➕ Crea un nuevo motivo de ingreso
 */
export const createMotivoIngreso = async (data: { nombre: string; descripcion?: string }) => {
  const response = await api.post('/parametros/motivos-ingreso', data)
  return response.data
}

/**
 * ✏️ Actualiza un motivo de ingreso
 */
export const updateMotivoIngreso = async (id: number, data: { nombre: string; descripcion?: string }) => {
  const response = await api.put(`/parametros/motivos-ingreso/${id}`, data)
  return response.data
}

/**
 * ❌ Elimina un motivo de ingreso
 */
export const deleteMotivoIngreso = async (id: number) => {
  const response = await api.delete(`/parametros/motivos-ingreso/${id}`)
  return response.data
}
