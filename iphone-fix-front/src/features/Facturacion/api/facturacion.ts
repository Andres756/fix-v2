// features/Facturacion/api/facturacion.ts
import http from '../../../shared/api/http'
import axios from 'axios'
import type {
  Factura,
  FiltrosFactura,
  FacturaListResponse,
  CreateFacturaVentaPayload,
  CreateFacturaServicioPayload,
  CreatePrefacturaPayload,
  RegistrarPagoPayload,
  AnularFacturaPayload,
  PagoFactura,
  ResumenFacturacion,
  FormaPago
} from '../types/factura'

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

// ========== FACTURAS ==========

/**
 * Listar facturas con filtros
 */
export async function fetchFacturas(filters?: FiltrosFactura): Promise<FacturaListResponse> {
  try {
    const params = new URLSearchParams()
    
    if (filters?.q) params.append('q', filters.q)
    if (filters?.estado) params.append('estado', filters.estado)
    if (filters?.tipo_venta) params.append('tipo_venta', filters.tipo_venta)
    if (filters?.cliente_id) params.append('cliente_id', filters.cliente_id.toString())
    if (filters?.desde) params.append('desde', filters.desde)
    if (filters?.hasta) params.append('hasta', filters.hasta)
    if (filters?.es_prefactura !== undefined) {
      params.append('es_prefactura', filters.es_prefactura ? '1' : '0')
    }
    if (filters?.entregado !== undefined) {
      params.append('entregado', filters.entregado ? '1' : '0')
    }
    if (filters?.page) params.append('page', filters.page.toString())
    if (filters?.per_page) params.append('per_page', filters.per_page.toString())
    
    const response = await http.get(`/facturacion/facturas?${params}`)
    return response.data
  } catch (error) {
    console.error('Error fetching facturas:', error)
    throw new Error(extractErrorMessage(error))
  }
}

/**
 * Obtener detalle de una factura
 */
export async function getFactura(facturaId: number): Promise<Factura> {
  try {
    const response = await http.get(`/facturacion/facturas/${facturaId}`)
    return unwrap(response)
  } catch (error) {
    console.error('Error fetching factura:', error)
    throw new Error(extractErrorMessage(error))
  }
}

/**
 * Crear factura de venta directa
 */
export async function createFacturaVenta(payload: CreateFacturaVentaPayload): Promise<Factura> {
  try {
    const response = await http.post('/facturacion/facturas', payload)
    return response.data.factura
  } catch (error) {
    console.error('Error creating factura venta:', error)
    throw new Error(extractErrorMessage(error))
  }
}

/**
 * Crear factura de servicio (orden)
 */
export async function createFacturaServicio(payload: CreateFacturaServicioPayload): Promise<Factura> {
  try {
    const response = await http.post('/facturacion/facturas', payload)
    return response.data.factura
  } catch (error) {
    console.error('Error creating factura servicio:', error)
    throw new Error(extractErrorMessage(error))
  }
}

/**
 * Prefacturar orden de servicio
 */
export async function prefacturarOrden(
  ordenId: number,
  payload?: {
    equipos_seleccionados?: number[]
    forma_pago_id?: number
    observaciones?: string
    entregado?: boolean
  }
): Promise<Factura> {
  try {
    const response = await http.post(`/facturacion/ordenes/${ordenId}/prefacturar`, payload || {})
    return response.data.factura || response.data
  } catch (error) {
    console.error('Error prefacturando orden:', error)
    throw new Error(extractErrorMessage(error))
  }
}

/**
 * Crear prefactura (cotización)
 */
export async function createPrefactura(payload: CreatePrefacturaPayload): Promise<Factura> {
  try {
    const response = await http.post('/facturacion/prefacturas', payload)
    return response.data.factura || response.data
  } catch (error) {
    console.error('Error creating prefactura:', error)
    throw new Error(extractErrorMessage(error))
  }
}

/**
 * Convertir prefactura a factura
 */
export async function convertirPrefactura(
  prefacturaId: number, 
  formaPagoId?: number
): Promise<Factura> {
  try {
    const response = await http.post(`/facturacion/prefacturas/${prefacturaId}/convertir`, {
      forma_pago_id: formaPagoId
    })
    return response.data.factura
  } catch (error) {
    console.error('Error converting prefactura:', error)
    throw new Error(extractErrorMessage(error))
  }
}

// ========== PAGOS ==========

/**
 * Listar pagos de una factura
 */
export async function fetchPagosFactura(facturaId: number): Promise<{
  message: string
  data: PagoFactura[]
}> {
  try {
    const response = await http.get(`/facturacion/facturas/${facturaId}/pagos`)
    return response.data
  } catch (error) {
    console.error('Error fetching pagos:', error)
    throw new Error(extractErrorMessage(error))
  }
}

/**
 * Registrar pago(s) a una factura
 */
export async function registrarPagos(
  facturaId: number, 
  payload: RegistrarPagoPayload
): Promise<{
  message: string
  factura: Factura
  vueltas: number
}> {
  try {
    const response = await http.post(`/facturacion/facturas/${facturaId}/pagos`, payload)
    return response.data
  } catch (error) {
    console.error('Error registering pagos:', error)
    throw new Error(extractErrorMessage(error))
  }
}

// ========== ANULACIÓN ==========

/**
 * Anular una factura completa
 */
export async function anularFactura(
  facturaId: number, 
  payload: AnularFacturaPayload
): Promise<{
  message: string
  factura: Factura
}> {
  try {
    const response = await http.patch(`/facturacion/facturas/${facturaId}/anular`, payload)
    return response.data
  } catch (error) {
    console.error('Error anulando factura:', error)
    throw new Error(extractErrorMessage(error))
  }
}

/**
 * Anulación avanzada (parcial por items)
 */
export async function anularFacturaAvanzado(
  facturaId: number,
  payload: {
    motivo: string
    items_anular: number[]
    observaciones?: string
  }
): Promise<{
  message: string
  factura: Factura
}> {
  try {
    const response = await http.patch(`/facturacion/facturas/${facturaId}/anular-avanzado`, payload)
    return response.data
  } catch (error) {
    console.error('Error en anulación avanzada:', error)
    throw new Error(extractErrorMessage(error))
  }
}

/**
 * Verificar si se puede anular una factura
 */
export async function verificarAnulacion(facturaId: number): Promise<{
  puede_anular: boolean
  mensaje?: string
}> {
  try {
    const response = await http.get(`/facturacion/facturas/${facturaId}/verificar-anulacion`)
    return response.data
  } catch (error) {
    console.error('Error verificando anulación:', error)
    throw new Error(extractErrorMessage(error))
  }
}

// ========== IMPRESIÓN/EXPORT ==========

/**
 * Marcar factura como entregada
 */
export async function entregarFactura(facturaId: number): Promise<{
  message: string
  factura: Factura
}> {
  try {
    const response = await http.patch(`/facturacion/facturas/${facturaId}/entregar`)
    return response.data
  } catch (error) {
    console.error('Error marcando entrega:', error)
    throw new Error(extractErrorMessage(error))
  }
}

/**
 * Entregar equipos específicos (solo órdenes de servicio)
 */
export async function entregarEquipos(
  facturaId: number,
  equiposIds: number[]
): Promise<{
  message: string
  factura: Factura
}> {
  try {
    const response = await http.patch(`/facturacion/facturas/${facturaId}/equiposentrega`, {
      equipos: equiposIds
    })
    return response.data
  } catch (error) {
    console.error('Error entregando equipos:', error)
    throw new Error(extractErrorMessage(error))
  }
}

/**
 * Obtener la URL del ticket para impresión
 */
export async function getFacturaPrintUrl(facturaId: number): Promise<string> {
  try {
    // El endpoint correcto según tu backend
    const response = await http.get(`/facturacion/facturas/${facturaId}/ticket`, {
      responseType: 'blob'
    })

    // Crear blob y abrir en nueva pestaña
    const blob = new Blob([response.data], { type: 'application/pdf' })
    const url = window.URL.createObjectURL(blob)
    window.open(url, '_blank')

    // También devolver la URL por si se necesita
    return url
  } catch (error) {
    console.error('Error al obtener el ticket:', error)
    throw new Error('Error al generar el ticket de factura')
  }
}

/**
 * Descargar ticket o PDF de factura
 * Usa el endpoint /ticket por defecto
 */
export async function downloadFacturaPDF(facturaId: number): Promise<void> {
  try {
    // Descarga directa del ticket (formato pequeño tipo POS)
    const response = await http.get(`/facturacion/facturas/${facturaId}/ticket`, {
      responseType: 'blob'
    })

    const blob = new Blob([response.data], { type: 'application/pdf' })
    const url = window.URL.createObjectURL(blob)

    // Abrir automáticamente en una nueva pestaña
    window.open(url, '_blank')
  } catch (error) {
    console.error('Error descargando factura (ticket):', error)
    throw new Error('Error al descargar el ticket de factura')
  }
}

// ========== RESUMEN/DASHBOARD ==========

/**
 * Obtener resumen de facturación
 */
export async function fetchResumenFacturacion(): Promise<ResumenFacturacion> {
  try {
    const response = await http.get('/facturacion/resumen')
    return response.data
  } catch (error) {
    console.error('Error fetching resumen:', error)
    throw new Error(extractErrorMessage(error))
  }
}

// ========== OPCIONES/PARÁMETROS ==========

/**
 * Obtener formas de pago disponibles
 */
export async function fetchFormasPago(): Promise<FormaPago[]> {
  try {
    const response = await http.get('/parametros/formas-pago/options')
    return unwrapArray(response)
  } catch (error) {
    console.error('Error fetching formas pago:', error)
    throw new Error(extractErrorMessage(error))
  }
}

/**
 * Obtener estados de factura
 */
export async function fetchEstadosFactura(): Promise<Array<{
  id: number
  codigo: string
  nombre: string
  color: string
}>> {
  try {
    const response = await http.get('/parametros/estados-factura/options')
    return unwrapArray(response)
  } catch (error) {
    console.error('Error fetching estados:', error)
    throw new Error(extractErrorMessage(error))
  }
}

/**
 * Obtener tipos de venta
 */
export async function fetchTiposVenta(): Promise<Array<{
  id: number
  codigo: string
  nombre: string
}>> {
  try {
    const response = await http.get('/parametros/tipos-venta/options')
    return unwrapArray(response)
  } catch (error) {
    console.error('Error fetching tipos venta:', error)
    throw new Error(extractErrorMessage(error))
  }

}

export async function fetchOrdenesPorCliente(clienteId: number) {
  const res = await http.get(`/clientes/${clienteId}/ordenes`)
  return res.data || []
}

export async function fetchEquiposDeOrden(clienteId: number, ordenId: number) {
  const res = await http.get(`/clientes/${clienteId}/ordenes/${ordenId}/equipos`)
  return res.data || []
}

export async function fetchMotivosAnulacion(): Promise<{ data: any[] }> {
  const res = await http.get('/facturacion/pagos/motivos-anulacion')
  return res.data
}

/**
 * Anular un pago de factura
 */
export async function anularPagoFactura(pagoId: number, motivoId: number) {
  const res = await http.put(`/facturacion/pagos/${pagoId}/anular`, {
    motivo_anulacion_id: motivoId,
  })
  return res.data
}
