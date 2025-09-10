import http from '../../../shared/api/http'
import type { OrdenServicio, CreateOrdenPayload, UpdateOrdenPayload } from '../types/orden'

/* ====== CREAR ORDEN ====== */
export async function createOrden(clienteId: number, payload: CreateOrdenPayload): Promise<OrdenServicio> {
  const { data } = await http.post(`/clientes/${clienteId}/ordenes`, payload)
  return data?.data ?? data
}

/* ====== LISTAR ORDENES DE UN CLIENTE ====== */
export async function fetchOrdenes(clienteId: number): Promise<OrdenServicio[]> {
  const { data } = await http.get(`/clientes/${clienteId}/ordenes`)
  return data?.data ?? data
}

/* ====== OBTENER ORDEN POR ID ====== */
export async function fetchOrden(clienteId: number, ordenId: number): Promise<OrdenServicio> {
  const { data } = await http.get(`/clientes/${clienteId}/ordenes/${ordenId}`)
  return data?.data ?? data
}

/* ====== ACTUALIZAR ORDEN ====== */
export async function updateOrden(clienteId: number, ordenId: number, payload: UpdateOrdenPayload): Promise<OrdenServicio> {
  const { data } = await http.put(`/clientes/${clienteId}/ordenes/${ordenId}`, payload)
  return data?.data ?? data
}

/* ====== HISTORIAL ORDEN ====== */
export async function fetchHistorialOrden(clienteId: number, ordenId: number): Promise<any[]> {
  const { data } = await http.get(`/clientes/${clienteId}/ordenes/${ordenId}/historial`)
  return data?.data ?? data
}

// GET /ordenes (global)
export async function fetchOrdenesGlobal(params?: { q?: string; estado?: string; cliente_id?: number; page?: number; per_page?: number }) {
  const res = await http.get('/ordenes', { params })
  return res.data
}

