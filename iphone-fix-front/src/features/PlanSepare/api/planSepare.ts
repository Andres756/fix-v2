// features/PlanSepare/api/planSepare.ts
import http from '../../../shared/api/http'
import type {
  PlanSepare,
  FiltrosPlanSepare,
  PlanSepareListResponse,
  CreatePlanSeparePayload,
  RegistrarAbonoPayload,
  AnularPlanSeparePayload,
  ReasignarPlanSeparePayload,
  ResumenPlanSepare,
  AbonoPlanSepare,
  DevolucionPlanSepare
} from '../types/planSepare'

import axios from 'axios'

// ===== Helpers para desempaquetar respuestas Laravel =====
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

// ===== Helper para extraer mensajes de error =====
function extractErrorMessage(error: any): string {
  // Errores de validación Laravel
  if (error.response?.data?.errors) {
    const errors = error.response.data.errors
    const firstErrorKey = Object.keys(errors)[0]
    const firstError = errors[firstErrorKey]
    return Array.isArray(firstError) ? firstError[0] : firstError
  }
  
  // Mensaje directo
  if (error.response?.data?.message) {
    return error.response.data.message
  }
  
  // Error 500
  if (error.response?.status === 500) {
    return 'Error del servidor. Por favor intente nuevamente.'
  }
  
  return error.message || 'Error desconocido'
}

// ========== PLANES SEPARE ==========

/**
 * Listar planes separe con filtros
 */
export async function fetchPlanesSepare(filters?: FiltrosPlanSepare): Promise<PlanSepareListResponse> {
  try {
    const params = new URLSearchParams()
    
    if (filters?.q) params.append('q', filters.q)
    if (filters?.estado_id) params.append('estado_id', filters.estado_id.toString())
    if (filters?.cliente_id) params.append('cliente_id', filters.cliente_id.toString())
    if (filters?.desde) params.append('desde', filters.desde)
    if (filters?.hasta) params.append('hasta', filters.hasta)
    if (filters?.proximos_vencer) params.append('proximos_vencer', '1')
    if (filters?.page) params.append('page', filters.page.toString())
    if (filters?.per_page) params.append('per_page', filters.per_page.toString())
    
    const response = await http.get(`/plan-separe?${params}`)
    return response.data
  } catch (error) {
    console.error('Error fetching planes separe:', error)
    throw new Error(extractErrorMessage(error))
  }
}

/**
 * Obtener detalle de un plan separe
 */
export async function getPlanSepare(planId: number): Promise<PlanSepare> {
  try {
    const response = await http.get(`/plan-separe/${planId}`)
    return unwrap(response)
  } catch (error) {
    console.error('Error fetching plan separe:', error)
    throw new Error(extractErrorMessage(error))
  }
}

/**
 * Crear nuevo plan separe
 */
export async function createPlanSepare(payload: CreatePlanSeparePayload): Promise<PlanSepare> {
  try {
    const response = await http.post('/plan-separe', payload)
    return response.data.plan || response.data
  } catch (error: any) {
    console.error('Error creating plan separe:', error)

    const msg =
      error?.response?.data?.error ||
      error?.response?.data?.message ||
      error?.message ||
      'Ocurrió un error inesperado.'

    // 🔹 Lanza un Error con el mensaje claro, para que el modal pueda mostrarlo
    throw new Error(msg)
  }
}

// ========== ABONOS ==========

/**
 * Listar abonos de un plan
 */
export async function fetchAbonosPlan(planId: number): Promise<AbonoPlanSepare[]> {
  try {
    const response = await http.get(`/plan-separe/${planId}/abonos`)
    return unwrapArray(response)
  } catch (error) {
    console.error('Error fetching abonos:', error)
    throw new Error(extractErrorMessage(error))
  }
}


/**
 * Registrar abono a un plan separe
 */
export async function registrarAbono(
  planId: number,
  payload: RegistrarAbonoPayload
): Promise<{
  message: string
  plan: PlanSepare
  abono: AbonoPlanSepare
  factura_generada?: boolean
}> {
  try {
    const response = await http.post(`/plan-separe/${planId}/abonos`, payload)

    // Si el plan alcanzó el 100%, se genera factura automáticamente
    if (response.data.factura_generada) {
      return {
        message: 'Plan completado y facturado exitosamente',
        plan: response.data.plan,
        abono: response.data.abono,
        factura_generada: true,
      }
    }

    return response.data
  } catch (error: any) {
    console.error('Error registrando abono:', error)

    // ✅ Mantener compatibilidad con tus validaciones existentes
    const message = error?.response?.data?.message || ''
    const backendError = error?.response?.data?.error

    if (message.includes('plan ya está facturado')) {
      throw new Error('Este plan ya fue facturado y no acepta más abonos')
    }

    if (message.includes('plan está cancelado')) {
      throw new Error('No se pueden registrar abonos en un plan cancelado')
    }

    if (message.includes('valor excede')) {
      throw new Error('El valor del abono excede el saldo pendiente')
    }

    // ✅ Si el backend envía un mensaje específico (como el del stock), lo propagamos intacto
    if (backendError) {
      // 👈 devolvemos el mismo objeto de error para no perder el response original
      throw error
    }

    // ✅ Fallback general
    throw new Error(message || error.message || 'Error al registrar el abono')
  }
}


// ========== ANULACIÓN Y DEVOLUCIÓN ==========

export async function fetchMotivosAnulacion() {
  const { data } = await http.get('/plan-separe/motivos-anulacion')
  return data as Array<{ id: number; nombre: string; descripcion?: string }>
}

/**
 * Anular plan separe (con o sin devolución)
 */
export async function anularPlanSepare(
  planId: number,
  payload: AnularPlanSeparePayload
): Promise<{
  message: string
  plan: PlanSepare
  devolucion?: DevolucionPlanSepare
}> {
  try {
    const response = await http.patch(`/plan-separe/${planId}/anular`, payload)
    return response.data
  } catch (error) {
    console.error('Error anulando plan separe:', error)
    
    const message = error.response?.data?.message || ''
    
    if (message.includes('ya está cancelado')) {
      throw new Error('Este plan ya está cancelado')
    }
    
    if (message.includes('ya está facturado')) {
      throw new Error('No se puede anular un plan que ya fue facturado')
    }
    
    throw new Error(extractErrorMessage(error))
  }
}

/**
 * Verificar si se puede anular un plan
 */
export async function verificarAnulacionPlan(planId: number): Promise<{
  puede_anular: boolean
  tiene_factura: boolean
  total_abonado: number
  mensaje?: string
}> {
  try {
    const response = await http.get(`/plan-separe/${planId}/verificar-anulacion`)
    return response.data
  } catch (error) {
    console.error('Error verificando anulación:', error)
    throw new Error(extractErrorMessage(error))
  }
}

// ========== REASIGNACIÓN ==========

/**
 * Reasignar plan separe a otro inventario
 */
export async function reasignarPlanSepare(
  planId: number,
  payload: ReasignarPlanSeparePayload
): Promise<{
  message: string
  plan: PlanSepare
}> {
  try {
    const response = await http.patch(`/plan-separe/${planId}/reasignar`, payload)
    return response.data
  } catch (error) {
    console.error('Error reasignando plan separe:', error)
    
    const message = error.response?.data?.message || ''
    
    if (message.includes('no se puede reasignar')) {
      throw new Error('Este plan no se puede reasignar porque ya tiene factura')
    }
    
    if (message.includes('producto no disponible')) {
      throw new Error('El producto seleccionado no está disponible')
    }
    
    if (message.includes('mismo producto')) {
      throw new Error('No se puede reasignar al mismo producto')
    }
    
    throw new Error(extractErrorMessage(error))
  }
}

// ========== RESUMEN Y REPORTES ==========

/**
 * Obtener resumen de planes separe
 */
export async function fetchResumenPlanSepare(): Promise<ResumenPlanSepare> {
  try {
    const response = await http.get('/plan-separe/resumen')
    return response.data
  } catch (error) {
    console.error('Error fetching resumen:', error)
    throw new Error(extractErrorMessage(error))
  }
}

/**
 * Obtener planes próximos a vencer
 */
export async function fetchPlanesProximosVencer(dias: number = 7): Promise<PlanSepare[]> {
  try {
    const response = await http.get(`/plan-separe/proximos-vencer?dias=${dias}`)
    return unwrapArray(response)
  } catch (error) {
    console.error('Error fetching planes próximos a vencer:', error)
    throw new Error(extractErrorMessage(error))
  }
}

/**
 * Obtener historial de auditoría de un plan
 */
export async function fetchAuditoriaPlan(planId: number): Promise<any[]> {
  try {
    const response = await http.get(`/plan-separe/${planId}/auditoria`)
    return unwrapArray(response)
  } catch (error) {
    console.error('Error fetching auditoría:', error)
    throw new Error(extractErrorMessage(error))
  }
}

// ========== VALIDACIONES ==========

/**
 * Verificar si un cliente puede crear un nuevo plan
 */
export async function verificarClientePlan(
  clienteId: number,
  inventarioId: number
): Promise<{
  puede_crear: boolean
  mensaje?: string
  plan_existente?: PlanSepare
}> {
  try {
    const response = await http.get(`/plan-separe/verificar-cliente`, {
      params: {
        cliente_id: clienteId,
        inventario_id: inventarioId
      }
    })
    return response.data
  } catch (error) {
    console.error('Error verificando cliente:', error)
    throw new Error(extractErrorMessage(error))
  }
}

/**
 * Calcular valores para un plan separe
 */
export async function calcularPlan(
  inventarioId: number,
  porcentajeMinimo: number
): Promise<{
  precio_comercial: number
  precio_sugerido: number
  valor_minimo: number
  valor_restante: number
}> {
  try {
    const response = await http.get('/plan-separe/calcular', {
      params: {
        inventario_id: inventarioId,
        porcentaje_minimo: porcentajeMinimo
      }
    })
    return response.data
  } catch (error) {
    console.error('Error calculando plan:', error)
    throw new Error(extractErrorMessage(error))
  }
}