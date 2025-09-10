import http from '../../../shared/api/http'
import type { Tecnico } from '../types/tecnico'

export interface FetchTecnicosParams {
  q?: string
  page?: number
  per_page?: number
}

export async function fetchTecnicos(
  params: FetchTecnicosParams = {}
): Promise<Tecnico[]> {
  const { data } = await http.get('/tecnicos', { params })
  // ⚡ Retorna solo el array con los técnicos
  return data.data as Tecnico[]
}
