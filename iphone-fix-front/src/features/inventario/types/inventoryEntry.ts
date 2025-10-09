// iphone-fix-front/src/features/inventario/types/inventoryEntry.ts

export interface EntradaInventario {
  id: number;
  motivo_ingreso_id: number;
  lote_id: number;
  fecha_entrada: string;
  observaciones?: string | null;
  created_at: string;
  updated_at: string;
  
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
  };
  
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
  proveedor_id: number;      // ✅ AGREGAR ESTA LÍNEA
  motivo_ingreso_id: number;
  lote_id: number;
  fecha_entrada: string;
  observaciones: string | null;
  items: {
    inventario_id: number;
    cantidad: number;
    costo_unitario: number;
  }[];
}