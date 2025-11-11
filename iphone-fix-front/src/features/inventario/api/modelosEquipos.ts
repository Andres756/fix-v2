// src/features/inventario/api/modelosEquipos.ts

import http from '../../../shared/api/http'
import type { ModeloEquipoOption, ReporteModeloInventario } from '../types/modeloEquipo'

export async function fetchModelosEquiposOptions(): Promise<ModeloEquipoOption[]> {
  const resp = await http.get('/inventario/modelos-equipos/options')
  return resp.data.data || []
}

export async function fetchReporteModelosInventario(params?: {
  familia?: string
  marca?: string
}): Promise<ReporteModeloInventario[]> {
  const resp = await http.get('/inventario/modelos-equipos/reporte', { params })
  return resp.data.data || []
}