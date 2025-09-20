import http from '../../../shared/api/http'
import type { EquipoAsignado, GananciasTecnico, HistorialTarea, Tecnico } from '../types/tecnico'

// Listar equipos asignados a un técnico
export async function fetchEquiposAsignados(tecnicoId: number): Promise<EquipoAsignado[]> {
  const { data } = await http.get(`/tecnicos/${tecnicoId}/equipos`)
  return data
}

// Resumen de ganancias del técnico
export async function fetchGanancias(tecnicoId: number): Promise<GananciasTecnico> {
  const { data } = await http.get(`/tecnicos/${tecnicoId}/ganancias`)
  return data
}

// Actualizar estado de tarea
export async function updateTareaEstado(
  tecnicoId: number,
  tareaId: number,
  estado: string
): Promise<any> {
  const { data } = await http.put(`/tecnicos/${tecnicoId}/tareas/${tareaId}/estado`, { estado })
  return data
}

// Ver historial de tarea
export async function fetchHistorialTarea(
  tecnicoId: number,
  tareaId: number
): Promise<HistorialTarea[]> {
  const { data } = await http.get(`/tecnicos/${tecnicoId}/tareas/${tareaId}/historial`)
  return data
}

// Listar todos los técnicos (para admin)
export async function fetchTecnicos(): Promise<Tecnico[]> {
  const { data } = await http.get('/tecnicos')
  return data.data as Tecnico[]  // ⚡ extraemos solo el array
}


