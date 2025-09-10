export type TipoImpuesto = 'n/a' | 'porcentaje' | 'fijo';

export interface MiniRef {
  id: number;
  nombre: string;
}

export interface LoteRef {
  id: number;
  codigo_lote: string;
}

export interface Inventario {
  id: number;
  nombre: string;
  nombre_detallado?: string | null;
  codigo?: string | null;

  // IDs (pueden venir como string desde el backend)
  tipo_inventario_id?: number | string;
  estado_inventario_id?: number | string;
  categoria_id?: number | string;

  // Relaciones (el backend puede mandarlas con uno u otro nombre)
  tipo?: MiniRef | null;
  tipo_inventario?: MiniRef | null;

  estado?: MiniRef | null;
  estado_inventario?: MiniRef | null;

  categoria?: MiniRef | null;
  proveedor?: MiniRef | null;
  lote?: LoteRef | null;

  // Números que a veces vienen como string
  stock: number | string;
  stock_minimo: number | string;

  precio: number | string;
  costo?: number | string;
  costo_mayor?: number | string;

  tipo_impuesto: TipoImpuesto;
  valor_impuesto: number | string;
  precio_final?: number | string;

  ruta_imagen?: string | null;
  imagen_url?: string | null;
  updated_at?: string | null;

  // Detalles (si los cargas en show)
  detalleEquipo?: any;
  detalleProducto?: any;
  detalleRepuesto?: any;
}
