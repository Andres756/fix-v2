// src/features/inventario/api/inventario.ts

import http from '../../../shared/api/http';
import type { Paginated } from '../../../shared/types/pagination';
import type { Option } from '../../../shared/types/common';
import type { Inventario, EntradaProducto, SalidaProducto } from '../types/inventario';

export interface FetchInventarioParams {
  q?: string;
  page?: number;
  per_page?: number;
  tipo_inventario_id?: number | null;
  categoria_id?: number | null;
  estado_inventario_id?: number | null;
  con_stock?: boolean
}

/* ====== INVENTARIOS ====== */
export async function fetchInventario(
  params: FetchInventarioParams = {}
): Promise<Paginated<Inventario>> {
  const { data } = await http.get('/inventario/inventarios', { params });
  return data as Paginated<Inventario>;
}

export async function fetchInventarioById(id: number): Promise<Inventario> {
  const { data } = await http.get(`/inventario/inventarios/${id}`);
  return (data?.data ?? data) as Inventario;
}

/* ====== CREAR PRODUCTO (SIN STOCK) ====== */
export interface CreateInventarioPayload {
  nombre: string;
  nombre_detallado?: string | null;
  codigo: string;

  tipo_inventario_id: number;
  categoria_id: number;

  // ELIMINADOS: estado_inventario_id, proveedor_id, lote_id, stock, costo

  stock_minimo: number;  // OBLIGATORIO
  precio: number;        // OBLIGATORIO > 0
  costo_mayor: number;   // OBLIGATORIO > 0

  tipo_impuesto?: 'n/a' | 'porcentaje' | 'fijo';
  valor_impuesto?: number;
  notas?: string;

  // Detalles por tipo
  detalle_equipo?: {
    imei_1: string;
    imei_2?: string | null;
    estado_fisico?: string | null;
    version_ios?: string | null;
    almacenamiento?: string | null;
    color?: string | null;
  };

  detalle_producto?: {
    material?: string | null;
    compatibilidad?: string | null;
    tipo_accesorio?: string | null;
  };

  detalle_repuesto?: {
    modelo_compatible?: string | null;
    tipo_repuesto?: string | null;
    referencia_fabricante?: string | null;
    garantia_meses?: number | null;
  };
}

// Helpers para FormData
function appendToFormData(form: FormData, key: string, value: any) {
  if (value === undefined || value === null) return;
  if (typeof value === 'boolean' || typeof value === 'number') {
    form.append(key, String(value));
    return;
  }
  if (value instanceof File || value instanceof Blob) {
    form.append(key, value);
    return;
  }
  form.append(key, value);
}

function objectToFormData(obj: any, form: FormData = new FormData(), namespace?: string): FormData {
  if (obj === undefined || obj === null) return form;

  if (obj instanceof File || obj instanceof Blob) {
    appendToFormData(form, namespace ?? 'file', obj);
    return form;
  }

  if (typeof obj !== 'object') {
    appendToFormData(form, namespace ?? 'value', obj);
    return form;
  }

  if (Array.isArray(obj)) {
    obj.forEach((v, i) => {
      const key = namespace ? `${namespace}[${i}]` : String(i);
      objectToFormData(v, form, key);
    });
    return form;
  }

  Object.keys(obj).forEach((prop) => {
    const value = obj[prop];
    const key = namespace ? `${namespace}[${prop}]` : prop;
    if (value instanceof Date) {
      appendToFormData(form, key, value.toISOString());
    } else if (typeof value === 'object' && value !== null && !(value instanceof File) && !(value instanceof Blob)) {
      objectToFormData(value, form, key);
    } else {
      appendToFormData(form, key, value);
    }
  });

  return form;
}

function hasFile(value?: File | null): value is File {
  return !!value && value instanceof File;
}

export async function createInventario(
  payload: CreateInventarioPayload,
  imagen?: File | null
): Promise<Inventario> {
  if (hasFile(imagen)) {
    const form = objectToFormData(payload);
    form.append('imagen', imagen);
    const { data } = await http.post('/inventario/inventarios', form);
    return (data?.data ?? data) as Inventario;
  } else {
    const { data } = await http.post('/inventario/inventarios', payload);
    return (data?.data ?? data) as Inventario;
  }
}

export type UpdateInventarioPayload = Partial<CreateInventarioPayload> & {
  eliminar_imagen?: boolean;
};

export async function updateInventario(
  id: number,
  payload: UpdateInventarioPayload,
  imagen?: File | null
): Promise<Inventario> {
  if (hasFile(imagen) || payload.eliminar_imagen) {
    const form = objectToFormData(payload);
    if (hasFile(imagen)) form.append('imagen', imagen);
    const { data } = await http.put(`/inventario/inventarios/${id}`, form);
    return (data?.data ?? data) as Inventario;
  } else {
    const { data } = await http.put(`/inventario/inventarios/${id}`, payload);
    return (data?.data ?? data) as Inventario;
  }
}

export async function deleteInventario(id: number): Promise<void> {
  await http.delete(`/inventario/inventarios/${id}`);
}

/* ====== ENTRADAS DE PRODUCTO ====== */
export interface CreateEntradaProductoPayload {
  inventario_id: number;
  lote_id: number;
  motivo_ingreso_id: number;
  cantidad: number;
  costo_unitario: number;
  fecha_entrada: string;
  observaciones?: string;
}

export async function createEntradaProducto(
  payload: CreateEntradaProductoPayload
): Promise<EntradaProducto> {
  const { data } = await http.post('/inventario/entradas-producto', payload);
  return (data?.data ?? data) as EntradaProducto;
}

export async function fetchEntradasProducto(
  params: { inventario_id?: number; lote_id?: number; per_page?: number } = {}
): Promise<Paginated<EntradaProducto>> {
  const { data } = await http.get('/inventario/entradas-producto', { params });
  return data as Paginated<EntradaProducto>;
}

/* ====== SALIDAS DE PRODUCTO ====== */
export interface CreateSalidaProductoPayload {
  inventario_id: number;
  tipo_salida: 'venta' | 'orden_servicio' | 'ajuste' | 'perdida';
  cantidad: number;
  referencia_id?: number | null;
  fecha_salida: string;
  observaciones?: string;
}

export async function createSalidaProducto(
  payload: CreateSalidaProductoPayload
): Promise<SalidaProducto> {
  const { data } = await http.post('/inventario/salidas-producto', payload);
  return (data?.data ?? data) as SalidaProducto;
}

export async function fetchSalidasProducto(
  params: { inventario_id?: number; tipo_salida?: string; per_page?: number } = {}
): Promise<Paginated<SalidaProducto>> {
  const { data } = await http.get('/inventario/salidas-producto', { params });
  return data as Paginated<SalidaProducto>;
}

/* ====== OPTIONS ====== */
export async function fetchTiposInventarioOptions(): Promise<Option[]> {
  const { data } = await http.get('/parametros/tipos-de-inventario/options');
  return data as Option[];
}

export async function fetchEstadosInventarioOptions(onlyVisible = true): Promise<Option[]> {
  const { data } = await http.get('/parametros/estados-inventario/options', {
    params: { solo_visibles: onlyVisible }
  });
  return data as Option[];
}

export async function fetchMotivosIngresoOptions(): Promise<Option[]> {
  const { data } = await http.get('/parametros/motivos-ingreso/options');
  return data as Option[];
}

export async function fetchCategoriasOptions(tipoId?: number): Promise<Option[]> {
  const { data } = await http.get('/inventario/categorias', {
    params: { per_page: 1000, tipo_inventario_id: tipoId }
  })
  const arr = Array.isArray(data?.data) ? data.data : data
  return (arr ?? []).map((c: any) => ({ id: Number(c.id), nombre: c.nombre }))
}

export async function fetchProveedoresOptions(): Promise<Option[]> {
  const { data } = await http.get('/inventario/proveedores', { params: { per_page: 1000 } });
  const arr = Array.isArray(data?.data) ? data.data : data;
  return (arr ?? []).map((p: any) => ({ id: Number(p.id), nombre: p.nombre }));
}

export async function fetchLotesOptions(): Promise<Option[]> {
  const { data } = await http.get('/inventario/lotes', { params: { per_page: 1000 } });
  const arr = Array.isArray(data?.data) ? data.data : data;
  return (arr ?? []).map((l: any) => ({
    id: Number(l.id),
    nombre: l.numero_lote ?? l.codigo_lote ?? `Lote #${l.id}`,  // Ajustado para numero_lote
  }));
}