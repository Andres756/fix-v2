// features/OrdenServicio/api/equipos.ts
import http from '../../../shared/api/http';
import type { Equipo, CreateEquipoPayload, UpdateEquipoPayload } from '../types/equipo'

/** ===== Helpers para desempaquetar respuestas Laravel Resource ===== */
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

/** ===== API ===== */
export async function fetchEquipos(
  clienteId: number,
  ordenId: number
): Promise<Equipo[]> {
  const resp = await http.get(`/clientes/${clienteId}/ordenes/${ordenId}/equipos`)
  return unwrapArray<Equipo>(resp)
}

export async function createEquipo(
  clienteId: number,
  ordenId: number,
  payload: CreateEquipoPayload
): Promise<Equipo> {
  const resp = await http.post(`/clientes/${clienteId}/ordenes/${ordenId}/equipos`, payload)
  return unwrap<Equipo>(resp)
}

export async function updateEquipo(
  clienteId: number,
  ordenId: number,
  equipoId: number,
  payload: UpdateEquipoPayload
): Promise<Equipo> {
  const resp = await http.put(
    `/clientes/${clienteId}/ordenes/${ordenId}/equipos/${equipoId}`,
    payload
  )
  return unwrap<Equipo>(resp)
}

export async function deleteEquipo(
  clienteId: number,
  ordenId: number,
  equipoId: number
): Promise<void> {
  await http.delete(`/clientes/${clienteId}/ordenes/${ordenId}/equipos/${equipoId}`)
}

export type { Equipo, CreateEquipoPayload, UpdateEquipoPayload }
