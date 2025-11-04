// features/PlanSepare/types/planSepare.ts

// ========== Estados del Plan Separe ==========
export enum EstadoPlanSepare {
  ABIERTO = 1,
  ASEGURADO = 2,
  FACTURADO = 3,
  EXPIRADO = 4,
  CANCELADO = 5,
  DEVUELTO = 6
}

// ========== Interfaces Base ==========
export interface Cliente {
  id: number
  nombre: string
  documento: string
  telefono?: string
  email?: string
}

export interface Inventario {
  id: number
  codigo: string
  nombre: string
  precio_venta: number
  stock_disponible: number
  imagen_url?: string
}

export interface FormaPago {
  id: number
  codigo: string
  nombre: string
}

// ========== Plan Separe Principal ==========
export interface PlanSepare {
  id: number
  codigo: string
  cliente_id: number
  cliente?: Cliente
  inventario_id: number
  inventario?: Inventario
  
  // Valores financieros
  precio_total: number
  porcentaje_minimo: number
  porcentaje_abonado: number
  total_abonado: number
  saldo_pendiente: number
  
  // Estados y control
  estado_id: EstadoPlanSepare
  estado?: {
    id: number
    codigo: string
    nombre: string
    color?: string
  }
  factura_id?: number
  factura?: {
    id: number
    codigo: string
  }
  
  // Fechas
  fecha_inicio: string
  fecha_limite: string
  fecha_asegurado?: string
  fecha_facturado?: string
  fecha_cancelacion?: string
  
  // Datos adicionales
  observaciones?: string
  usuario_id: number
  usuario?: {
    id: number
    name: string
  }
  
  // Relaciones
  abonos?: AbonoPlanSepare[]
  devolucion?: DevolucionPlanSepare
  auditoria?: AuditoriaPlanSepare[]
  
  // Timestamps
  created_at: string
  updated_at: string
}

// ========== Abono ==========
export interface AbonoPlanSepare {
  id: number
  plan_separe_id: number
  forma_pago_id: number
  forma_pago?: FormaPago
  valor: number
  observaciones?: string
  usuario_id: number
  usuario?: {
    id: number
    name: string
  }
  created_at: string
}

// ========== Devolución ==========
export interface DevolucionPlanSepare {
  id: number
  plan_separe_id: number
  monto_total: number
  monto_devuelto: number
  porcentaje_devolucion: number
  forma_pago_id: number
  forma_pago?: FormaPago
  motivo: string
  observaciones?: string
  usuario_id: number
  usuario?: {
    id: number
    name: string
  }
  fecha_devolucion: string
  created_at: string
}

// ========== Auditoría ==========
export interface AuditoriaPlanSepare {
  id: number
  plan_separe_id: number
  accion: string
  descripcion: string
  usuario_id: number
  usuario?: {
    id: number
    name: string
  }
  created_at: string
}

// ========== Payloads ==========
export interface CreatePlanSeparePayload {
  cliente_id: number
  inventario_id: number
  precio_total: number
  porcentaje_minimo: number
  observaciones?: string
}

export interface RegistrarAbonoPayload {
  valor: number
  forma_pago_id: number
  observaciones?: string
}

export interface AnularPlanSeparePayload {
  motivo: string
  porcentaje_devolucion?: number
  forma_pago_id?: number
  observaciones?: string
}

export interface ReasignarPlanSeparePayload {
  nuevo_inventario_id: number
  observaciones?: string
}

// ========== Filtros ==========
export interface FiltrosPlanSepare {
  q?: string
  estado_id?: number
  cliente_id?: number
  desde?: string
  hasta?: string
  proximos_vencer?: boolean
  page?: number
  per_page?: number
}

// ========== Respuestas ==========
export interface PlanSepareListResponse {
  data: PlanSepare[]
  meta?: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}

export interface ResumenPlanSepare {
  total_planes_activos: number
  total_planes_asegurados: number
  total_abonado_mes: number
  planes_proximos_vencer: number
}

// ========== Helpers ==========
export const EstadoPlanSepareColors: Record<number, string> = {
  1: 'yellow',  // ABIERTO
  2: 'blue',    // ASEGURADO
  3: 'green',   // FACTURADO
  4: 'gray',    // EXPIRADO
  5: 'red',     // CANCELADO
  6: 'purple'   // DEVUELTO
}

export const EstadoPlanSepareLabels: Record<number, string> = {
  1: 'Abierto',
  2: 'Asegurado',
  3: 'Facturado',
  4: 'Expirado',
  5: 'Cancelado',
  6: 'Devuelto'
}