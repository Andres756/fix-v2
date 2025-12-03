// iphone-fix-front/src/features/inventario/types/inventoryEntry.ts

export interface EstadoEntrada {
  id: number;
  nombre: string;
  codigo: string;
  color: string;
  descripcion?: string;
}

export interface Proveedor {
  id: number;
  nombre: string;
  nit?: string;
  tipo_documento?: 'NIT' | 'CC' | 'CE' | 'PASAPORTE';
  telefono?: string;
  correo?: string;
  direccion?: string;
}

export interface Cliente {
  id: number;
  nombre: string;
  documento?: string;
  telefono?: string;
  correo?: string;
  direccion?: string;
}

export interface Lote {
  id: number;
  numero_lote: string;
  proveedor_id?: number | null;
  costo_flete?: number;
  fecha_ingreso?: string;
  notas?: string;
  proveedor?: Proveedor | null;
}

export interface Origen {
  tipo: 'proveedor' | 'cliente';
  id: number;
  nombre: string;
  nit?: string;
  documento?: string;
}

export interface EntradaInventario {
  id: number;
  tipo_entrada: 'proveedor' | 'cliente';
  proveedor_id?: number | null;
  cliente_id?: number | null;
  motivo_ingreso_id: number;
  estado_entrada_id: number;
  lote_id?: number | null;
  fecha_entrada: string;
  observaciones?: string | null;
  usuario_id?: number | null;
  total_entrada: number;
  created_at: string;
  updated_at: string;
  
  proveedor?: Proveedor | null;
  cliente?: Cliente | null;
  origen?: Origen;
  
  motivo_ingreso?: {
    id: number;
    nombre: string;
  };

  estado_entrada?: EstadoEntrada;
  
  lote?: Lote | null;

  usuario?: {
    id: number;
    name: string;
  } | null;
  
  items?: EntradaInventarioItem[];
}

export interface EntradaInventarioItem {
  id: number;
  entrada_id: number;
  inventario_id: number;
  cantidad: number;
  costo_unitario: number;
  costo_flete_asignado?: number; // 🆕 Flete distribuido
  costo_total_item?: number; // 🆕 Total calculado
  
  inventario?: {
    id: number;
    nombre: string;
    codigo: string;
    stock?: number;
    costo?: number;
  };
}

export interface CreateEntradaInventarioPayload {
  tipo_entrada: 'proveedor' | 'cliente';
  proveedor_id?: number | null;
  cliente_id?: number | null;
  motivo_ingreso_id: number;
  estado_entrada_id?: number;
  lote_id?: number | null;
  fecha_entrada: string;
  observaciones?: string | null;
  items: {
    inventario_id: number;
    cantidad: number;
    costo_unitario: number;
  }[];
}

export interface UpdateEstadoEntradaPayload {
  estado_entrada_id: number;
}

// 🆕 Payload para asignar/actualizar lote con distribución de flete
export interface AsignarLotePayload {
  lote_id: number;
  distribucion_flete?: {
    item_id: number;
    costo_flete_asignado: number;
  }[];
}

// 🆕 Para crear un nuevo lote
export interface CreateLotePayload {
  numero_lote: string;
  proveedor_id?: number | null;
  costo_flete?: number;
  fecha_ingreso?: string;
  notas?: string;
}