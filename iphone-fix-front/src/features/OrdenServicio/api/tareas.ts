// features/OrdenServicio/api/tareas.ts
import http from '../../../shared/api/http'
import type { Tarea, CreateTareaPayload, UpdateTareaPayload } from '../types/tarea'

function unwrap<T>(axiosResp: any): T {
  const payload = axiosResp?.data ?? axiosResp
  return payload && typeof payload === 'object' && 'data' in payload
    ? (payload.data as T)
    : (payload as T)
}
function unwrapArray<T>(axiosResp: any): T[] {
  const out = unwrap<any>(axiosResp)
  return Array.isArray(out) ? out : []
}

export async function fetchTareas(
  clienteId: number,
  ordenId: number,
  equipoId: number
): Promise<Tarea[]> {
  const r = await http.get(`/clientes/${clienteId}/ordenes/${ordenId}/equipos/${equipoId}/tareas`)
  return unwrapArray<Tarea>(r)
}

export async function createTarea(
  clienteId: number,
  ordenId: number,
  equipoId: number,
  payload: CreateTareaPayload
): Promise<Tarea> {
  const r = await http.post(`/clientes/${clienteId}/ordenes/${ordenId}/equipos/${equipoId}/tareas`, payload)
  return unwrap<Tarea>(r)
}

export async function updateTarea(
  clienteId: number,
  ordenId: number,
  equipoId: number,
  tareaId: number,
  payload: UpdateTareaPayload
): Promise<Tarea> {
  const r = await http.put(`/clientes/${clienteId}/ordenes/${ordenId}/equipos/${equipoId}/tareas/${tareaId}`, payload)
  return unwrap<Tarea>(r)
}

export async function deleteTarea(
  clienteId: number,
  ordenId: number,
  equipoId: number,
  tareaId: number
): Promise<void> {
  await http.delete(`/clientes/${clienteId}/ordenes/${ordenId}/equipos/${equipoId}/tareas/${tareaId}`)
}

export type { Tarea, CreateTareaPayload, UpdateTareaPayload }
