// api/repuestosExternos.ts
import http from '../../../shared/api/http'
import type { RepuestoExterno, CreateRepuestoExternoPayload } from '../types/repuestoExterno'

export async function fetchRepuestosExternos(
  clienteId: number,
  ordenId: number,
  equipoId: number
): Promise<RepuestoExterno[]> {
  const { data } = await http.get(`/clientes/${clienteId}/ordenes/${ordenId}/equipos/${equipoId}/repuestos-externos`)
  return data.data
}

export async function createRepuestoExterno(
  clienteId: number,
  ordenId: number,
  equipoId: number,
  payload: CreateRepuestoExternoPayload
): Promise<RepuestoExterno> {
  const { data } = await http.post(`/clientes/${clienteId}/ordenes/${ordenId}/equipos/${equipoId}/repuestos-externos`, payload)
  return data.data
}

export async function deleteRepuestoExterno(
  clienteId: number,
  ordenId: number,
  equipoId: number,
  repuestoId: number
) {
  await http.delete(`/clientes/${clienteId}/ordenes/${ordenId}/equipos/${equipoId}/repuestos-externos/${repuestoId}`)
}
