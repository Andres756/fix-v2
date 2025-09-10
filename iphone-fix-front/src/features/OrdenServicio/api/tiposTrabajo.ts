// features/OrdenServicio/api/tiposTrabajo.ts
import http from '../../../shared/api/http'

export type TipoTrabajo = {
  id: number
  nombre: string
  // Valor normalizado que usará el front
  costo: number
  // (opcional) valor original si la API lo envía como costo_sugerido
  costo_sugerido?: number
}

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

export async function fetchTiposTrabajo(): Promise<TipoTrabajo[]> {
  // Ruta actual: /tipos-trabajo
  const r = await http.get('/tipos-trabajo')
  const arr = unwrapArray<any>(r)

  return arr.map((it: any) => {
    const costo = Number(
      it.costo ??
      it.costo_sugerido ??   // 👈 ahora soportado
      it.costo_sugerido ??
      it.costo_base ??
      it.precio ??
      0
    )
    return {
      id: Number(it.id ?? it.value),
      nombre: String(it.nombre ?? it.label ?? ''),
      costo,
      // lo preservamos por si lo necesitas en otro lugar
      costo_sugerido: it.costo_sugerido != null ? Number(it.costo_sugerido) : undefined,
    }
  })
}
