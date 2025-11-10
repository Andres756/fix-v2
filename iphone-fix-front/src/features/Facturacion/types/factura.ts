// features/Facturacion/types/factura.ts

// ========== Tipos Base ==========
export interface Cliente {
  id: number
  nombre: string
  documento: string
  telefono?: string | null
  email?: string
  direccion?: string | null
}

export interface FormaPago {
  id: number
  codigo: string
  nombre: string
  requiere_referencia?: boolean
}

export interface EstadoFactura {
  id: number
  codigo: 'PEND' | 'PAGA' | 'PARC' | 'ANUL'
  nombre: string
  color?: string
}

export interface TipoVenta {
  id: number
  codigo: 'VD' | 'SRV' | 'PS'
  nombre: string
}

// ========== Factura Principal ==========
export interface Factura {
  id: number
  codigo: string
  cliente_id: number
  cliente?: Cliente
  usuario_id: number
  usuario?: {
    id: number
    name: string
  }
  direccion?: string

  
  // Tipo y estado
  tipo_venta_id: number
  tipo_venta?: TipoVenta
  estado_id: number
  estado?: {
    codigo: string;
    nombre: string;
  };
  // Montos
  subtotal: number
  impuestos: number
  descuentos: number
  total: number
  total_pagado?: number
  saldo_pendiente?: number
  
  // Referencias
  orden_servicio_id?: number
  orden_servicio?: {
    id: number
    codigo: string
    descripcion?: string
  }
  plan_separe_id?: number
  
  // Control
  es_prefactura: boolean
  entregado: boolean
  fecha_emision: string
  fecha_vencimiento?: string
  observaciones?: string
  
  // Relaciones
  detalles?: FacturaDetalle[]
  pagos?: PagoFactura[]
  auditoria?: FacturaAuditoria[]
  
  // Timestamps
  created_at: string
  updated_at: string
}

// ========== Detalle de Factura ===========
export interface FacturaDetalle {
  id: number
  factura_id: number
  
  // Producto/Servicio
  inventario_id?: number
  inventario?: {
    id: number
    codigo: string
    nombre: string
    precio_venta: number
    precio_mayorista?: number
  }
  equipo_orden_id?: number
  equipo?: {
    id: number
    modelo: string
    imei?: string
  }
  
  // Datos del item
  descripcion: string
  cantidad: number
  precio_unitario: number
  descuento: number
  impuesto: number
  total: number
  
  // Control
  tipo: 'producto' | 'servicio' | 'repuesto'
  created_at: string

  // Nuevo campo para controlar el estado
  estado?: {
    codigo: string;
  };
}

// ========== Pagos ==========
export interface PagoFactura {
  id: number
  factura_id: number
  forma_pago_id: number
  forma_pago?: FormaPago
  
  valor: number
  referencia_externa?: string
  observaciones?: string
  
  usuario_id: number
  usuario?: {
    id: number
    name: string
  }
  
  created_at: string
  updated_at: string
}

// ========== Auditoría/Anulación ==========
export interface FacturaAuditoria {
  id: number
  factura_id: number
  accion: 'CREACION' | 'MODIFICACION' | 'ANULACION' | 'PAGO'
  descripcion: string
  datos_anteriores?: any
  datos_nuevos?: any
  
  usuario_id: number
  usuario?: {
    id: number
    name: string
  }
  
  created_at: string
}

export interface AnulacionFactura {
  id: number
  factura_id: number
  motivo: string
  observaciones?: string
  
  usuario_id: number
  usuario?: {
    id: number
    name: string
  }
  
  created_at: string
}

// ========== Payloads para crear/actualizar ==========
export interface CreateFacturaVentaPayload {
  origen: 'venta'
  cliente_id: number
  forma_pago_id?: number
  observaciones?: string
  monto_recibido?: number
  entregado?: boolean
  
  items: Array<{
    inventario_id: number
    cantidad: number
    tipo_precio?: 'DET' | 'MAY'
    descuento?: number
  }>
  
  pagos?: Array<{
    forma_pago_id: number
    valor: number
    referencia_externa?: string
  }>
}

export interface CreateFacturaServicioPayload {
  origen: 'servicio'
  orden_servicio_id: number
  cliente_id?: number
  forma_pago_id?: number
  observaciones?: string
  entregado?: boolean
  equipos_seleccionados?: number[]
  
  pagos?: Array<{
    forma_pago_id: number
    valor: number
    referencia_externa?: string
  }>
}

export interface CreatePrefacturaPayload {
  cliente_id: number
  items: Array<{
    inventario_id?: number
    descripcion: string
    cantidad: number
    precio_unitario: number
  }>
  observaciones?: string
}

export interface RegistrarPagoPayload {
  pagos: Array<{
    forma_pago_id: number
    valor: number
    observaciones?: string
    referencia_externa?: string
  }>
  monto_recibido?: number
}

export interface AnularFacturaPayload {
  motivo: string
  observaciones?: string
}

// ========== Filtros y búsqueda ==========
export interface FiltrosFactura {
  q?: string // búsqueda general
  estado?: string
  tipo_venta?: string
  cliente_id?: number
  desde?: string
  hasta?: string
  es_prefactura?: boolean
  entregado?: boolean
  page?: number
  per_page?: number
}

// ========== Respuestas del API ==========
export interface FacturaListResponse {
  data: Factura[]
  meta?: {
    current_page: number
    last_page: number
    per_page: number
    total: number
        // 🔹 Campos calculados dinámicamente por el backend
    total_pagado?: number
    saldo_pendiente?: number
  }
}

export interface ResumenFacturacion {
  total_ventas_dia: number
  total_ventas_mes: number
  facturas_pendientes: number
  facturas_anuladas_mes: number
}

// ========== Helpers de estado ==========
export const EstadoColors: Record<string, string> = {
  PEND: 'yellow',
  PARC: 'blue',
  PAGA: 'green',
  ANUL: 'red'
}

export const TipoVentaLabels: Record<string, string> = {
  VD: 'Venta Directa',
  SRV: 'Servicio',
  PS: 'Plan Separe'
}