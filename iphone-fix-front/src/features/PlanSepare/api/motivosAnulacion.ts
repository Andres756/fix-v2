// src/features/PlanSepare/api/motivosAnulacion.ts
import axios from 'axios'

export async function fetchMotivosAnulacion() {
  const { data } = await axios.get('/api/plan-separe/motivos-anulacion')
  return data as Array<{ id: number; nombre: string; descripcion?: string }>
}
