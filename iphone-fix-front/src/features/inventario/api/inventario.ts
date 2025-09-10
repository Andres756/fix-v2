import http from '../../../shared/api/http';
import type { Paginated } from '../../../shared/types/pagination';
import type { Option } from '../../../shared/types/common';
import type { Inventario } from '../types/inventario';

export interface FetchInventarioParams {
  q?: string;
  page?: number;
  per_page?: number;
  tipo_inventario_id?: number | null;
  categoria_id?: number | null;
  estado_inventario_id?: number | null;
}

/* ====== LISTAR / PAGINAR ====== */
export async function fetchInventario(
  params: FetchInventarioParams = {}
): Promise<Paginated<Inventario>> {
  const { data } = await http.get('/inventario/inventarios', { params });
  // Laravel Resource collection -> { data, meta, links }
  return data as Paginated<Inventario>;
}

/* ====== OBTENER UNO ====== */
export async function fetchInventarioById(id: number): Promise<Inventario> {
  const { data } = await http.get(`/inventario/inventarios/${id}`);
  return (data?.data ?? data) as Inventario;
}

/* ====== CREAR ====== */
export interface CreateInventarioPayload {
  nombre: string;
  nombre_detallado?: string | null;
  codigo?: string | null;

  tipo_inventario_id: number;
  categoria_id?: number | null;
  estado_inventario_id?: number | null;
  proveedor_id?: number | null;
  lote_id?: number | null;

  stock?: number;
  stock_minimo?: number;

  precio: number;
  costo: number;
  costo_mayor?: number;

  tipo_impuesto?: 'n/a' | 'porcentaje' | 'fijo';
  valor_impuesto?: number;

  // detalles por tipo (opcionales)
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

// ---------- Helpers ----------
function appendToFormData(form: FormData, key: string, value: any) {
  if (value === undefined || value === null) return;
  // Si es boolean/number, convertir a string para evitar "[object Object]"
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

// ---------- Crear / Actualizar con soporte de imagen ----------
export async function createInventario(
  payload: CreateInventarioPayload,
  imagen?: File | null
): Promise<Inventario> {
  if (hasFile(imagen)) {
    const form = objectToFormData(payload);
    form.append('imagen', imagen); // <-- en Laravel: $request->file('imagen')
    const { data } = await http.post('/inventario/inventarios', form);
    return (data?.data ?? data) as Inventario;
  } else {
    const { data } = await http.post('/inventario/inventarios', payload);
    return (data?.data ?? data) as Inventario;
  }
}

/* ====== ACTUALIZAR ====== */
export type UpdateInventarioPayload = Partial<CreateInventarioPayload> & {
  eliminar_imagen?: boolean; // opcional: por si quieres marcar para borrar
};

export async function updateInventario(
  id: number,
  payload: UpdateInventarioPayload,
  imagen?: File | null
): Promise<Inventario> {
  if (hasFile(imagen) || payload.eliminar_imagen) {
    const form = objectToFormData(payload);
    if (hasFile(imagen)) form.append('imagen', imagen);
    // Nota: Laravel acepta PUT con multipart. Si tu backend requiere POST + _method, usa:
    // const form = objectToFormData({ ...payload, _method: 'PUT' });
    const { data } = await http.put(`/inventario/inventarios/${id}`, form);
    return (data?.data ?? data) as Inventario;
  } else {
    const { data } = await http.put(`/inventario/inventarios/${id}`, payload);
    return (data?.data ?? data) as Inventario;
  }
}

/* ====== ELIMINAR ====== */
export async function deleteInventario(id: number): Promise<void> {
  await http.delete(`/inventario/inventarios/${id}`);
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

export async function fetchCategoriasOptions(tipoId?: number): Promise<Option[]> {
  const { data } = await http.get('/inventario/categorias', {
    params: { per_page: 1000, tipo_inventario_id: tipoId }
  })
  const arr = Array.isArray(data?.data) ? data.data : data
  return (arr ?? []).map((c: any) => ({ id: Number(c.id), nombre: c.nombre }))
}

export async function fetchProveedoresOptions(): Promise<Option[]> {
  // ideal: un endpoint /inventario/proveedores/options
  // fallback: /inventario/proveedores?per_page=1000 y mapear {id, nombre}
  const { data } = await http.get('/inventario/proveedores', { params: { per_page: 1000 } });
  const arr = Array.isArray(data?.data) ? data.data : data;
  return (arr ?? []).map((p: any) => ({ id: Number(p.id), nombre: p.nombre }));
}

export async function fetchLotesOptions(): Promise<Option[]> {
  const { data } = await http.get('/inventario/lotes', { params: { per_page: 1000 } });
  const arr = Array.isArray(data?.data) ? data.data : data;
  return (arr ?? []).map((l: any) => ({
    id: Number(l.id),
    nombre: l.nombre ?? l.codigo_lote ?? `Lote #${l.id}`,
  }));
}
