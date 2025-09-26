// src/features/inventario/types/inventario.ts

export type TipoImpuesto = 'n/a' | 'porcentaje' | 'fijo';

export interface MiniRef {
  id: number;
  nombre: string;
}

export interface Inventario {
  id: number;
  nombre: string;
  nombre_full?: string | null;
  codigo?: string | null;

  // IDs
  tipo_inventario_id?: number | string;
  estado_inventario_id?: number | string;
  categoria_id?: number | string;

  // ELIMINADOS: proveedor_id, lote_id

  // Relaciones
  tipo?: MiniRef | null;
  tipo_inventario?: MiniRef | null;

  estado?: MiniRef | null;
  estado_inventario?: MiniRef | null;

  categoria?: MiniRef | null;

  // Stock y precios
  stock: number | string;
  stock_minimo: number | string;

  precio: number | string;
  costo?: number | string;
  costo_mayor?: number | string;

  tipo_impuesto: TipoImpuesto;
  valor_impuesto: number | string;

  ruta_imagen?: string | null;
  imagen_url?: string | null;
  updated_at?: string | null;

  // Detalles (si los cargas en show)
  detalleEquipo?: any;
  detalleProducto?: any;
  detalleRepuesto?: any;
}

// NUEVO: Tipos para movimientos de inventario
export interface EntradaProducto {
  id: number;
  inventario_id: number;
  lote_id: number;
  motivo_ingreso_id: number;
  cantidad: number;
  costo_unitario: number | string;
  fecha_entrada: string;
  observaciones?: string | null;
  
  inventario?: {
    id: number;
    nombre: string;
    codigo: string;
    stock: number;
    costo: string;
  };
  
  lote?: {
    id: number;
    numero_lote: string;
    proveedor?: {
      id: number;
      nombre: string;
    } | null;
  };
  
  motivo?: {
    id: number;
    nombre: string;
  };
}

export interface SalidaProducto {
  id: number;
  inventario_id: number;
  tipo_salida: 'venta' | 'orden_servicio' | 'ajuste' | 'perdida';
  cantidad: number;
  costo_unitario: number | string;
  referencia_id?: number | null;
  fecha_salida: string;
  observaciones?: string | null;
  
  inventario?: {
    id: number;
    nombre: string;
    codigo: string;
    stock: number;
  };
  
  tipo_salida_label?: string;
}