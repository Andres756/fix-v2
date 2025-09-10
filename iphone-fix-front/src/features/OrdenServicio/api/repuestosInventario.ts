import http from '../../../shared/api/http'
import type { RepuestoInventario, CreateRepuestoInventarioPayload } from '../types/repuestoInventario'

// Listar repuestos de un equipo
export async function fetchRepuestosInventario(clienteId: number, ordenId: number, equipoId: number): Promise<RepuestoInventario[]> {
  const { data } = await http.get(`/clientes/${clienteId}/ordenes/${ordenId}/equipos/${equipoId}/repuestos-inventario`)
  return data.data
}

// Crear un repuesto en el equipo
export async function createRepuestoInventario(clienteId: number, ordenId: number, equipoId: number, payload: CreateRepuestoInventarioPayload): Promise<RepuestoInventario> {
  const { data } = await http.post(`/clientes/${clienteId}/ordenes/${ordenId}/equipos/${equipoId}/repuestos-inventario`, payload)
  return data.data
}

// Eliminar un repuesto del equipo
export async function deleteRepuestoInventario(clienteId: number, ordenId: number, equipoId: number, repuestoId: number): Promise<void> {
  await http.delete(`/clientes/${clienteId}/ordenes/${ordenId}/equipos/${equipoId}/repuestos-inventario/${repuestoId}`)
}
