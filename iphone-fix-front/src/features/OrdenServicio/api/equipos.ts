// features/OrdenServicio/api/equipos.ts
import http from '../../../shared/api/http';
import type { Equipo, CreateEquipoPayload, UpdateEquipoPayload, EquipoCostos } from '../types/equipo'

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

/** 
 * Helper para extraer mensaje de error desde la respuesta del backend 
 */
function extractErrorMessage(error: any): string {
  // 1. Verificar si hay errores de validación (formato Laravel)
  if (error.response?.data?.errors) {
    const errors = error.response.data.errors;
    
    // Obtener el primer error de validación
    const firstErrorKey = Object.keys(errors)[0];
    const firstError = errors[firstErrorKey];
    
    // El error puede ser un array o string
    return Array.isArray(firstError) ? firstError[0] : firstError;
  }
  
  // 2. Verificar si hay un mensaje directo
  if (error.response?.data?.message) {
    return error.response.data.message;
  }
  
  // 3. Errores de IMEI duplicado (500 con SQLSTATE)
  if (error.response?.status === 500) {
    const errorMsg = error.response?.data?.message || '';
    
    if (errorMsg.includes('Duplicate entry') && errorMsg.includes('unique_orden_imei')) {
      return 'Este IMEI/Serial ya está registrado en esta orden de servicio';
    }
    
    if (errorMsg.includes('SQLSTATE')) {
      return 'Error al guardar el equipo. Por favor verifica los datos.';
    }
  }
  
  // 4. Mensaje genérico si no hay nada específico
  return 'Ocurrió un error al procesar la solicitud';
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
  try {
    const resp = await http.post(`/clientes/${clienteId}/ordenes/${ordenId}/equipos`, payload)
    return unwrap<Equipo>(resp)
  } catch (error: any) {
    // Extraer el mensaje de error y lanzarlo
    const errorMessage = extractErrorMessage(error);
    throw new Error(errorMessage);
  }
}

export async function updateEquipo(
  clienteId: number,
  ordenId: number,
  equipoId: number,
  payload: UpdateEquipoPayload
): Promise<Equipo> {
  try {
    const resp = await http.put(
      `/clientes/${clienteId}/ordenes/${ordenId}/equipos/${equipoId}`,
      payload
    )
    return unwrap<Equipo>(resp)
  } catch (error: any) {
    // Extraer el mensaje de error y lanzarlo
    const errorMessage = extractErrorMessage(error);
    throw new Error(errorMessage);
  }
}

export async function deleteEquipo(
  clienteId: number,
  ordenId: number,
  equipoId: number
): Promise<void> {
  await http.delete(`/clientes/${clienteId}/ordenes/${ordenId}/equipos/${equipoId}`)
}

export async function getEquipoCostos(equipoId: number): Promise<EquipoCostos> {
  const resp = await http.get(`/equipos/${equipoId}/costos`);
  return resp.data as EquipoCostos;
}

export type { Equipo, CreateEquipoPayload, UpdateEquipoPayload }