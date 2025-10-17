// iphone-fix-front/src/features/inventario/types/inventoryEntry.ts

export interface EntradaInventario {
  id: number;
  proveedor_id: number;
  motivo_ingreso_id: number;
  lote_id: number | null;
  fecha_entrada: string;
  observaciones?: string | null;
  created_at: string;
  updated_at: string;
  
  proveedor?: {
    id: number;
    nombre: string;
  };
  
  motivo?: {
    id: number;
    nombre: string;
  };
  
  lote?: {
    id: number;
    numero_lote: string;
    proveedor?: {
      id: number;
      nombre: string;
    } | null;
  } | null;
  
  items?: EntradaInventarioItem[];
}

export interface EntradaInventarioItem {
  id: number;
  entrada_id: number;
  inventario_id: number;
  cantidad: number;
  costo_unitario: number;
  
  inventario?: {
    id: number;
    nombre: string;
    codigo: string;
  };
}

export interface CreateEntradaInventarioPayload {
  proveedor_id: number;
  motivo_ingreso_id: number;
  lote_id: number | null;         // ✅ Permite null
  fecha_entrada: string;
  observaciones: string | null;
  items: {
    inventario_id: number;
    cantidad: number;
    costo_unitario: number;
  }[];
}