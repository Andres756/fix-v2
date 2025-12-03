// iphone-fix-front/src/features/inventario/api/inventoryEntries.ts

import http from '../../../shared/api/http';
import type { Paginated } from '../../../shared/types/pagination';
import type { 
  EntradaInventario, 
  CreateEntradaInventarioPayload,
  UpdateEstadoEntradaPayload,
  EstadoEntrada,
  Proveedor,
  Cliente,
  Lote,
  CreateLotePayload,
  AsignarLotePayload
} from '../types/inventoryEntry';

/**
 * Crea una nueva entrada de inventario con múltiples productos
 */
export async function createEntradaInventario(
  payload: CreateEntradaInventarioPayload
): Promise<EntradaInventario> {
  const { data } = await http.post('/inventario/entradas-producto', payload);
  return (data?.data ?? data) as EntradaInventario;
}

/**
 * Obtiene todas las entradas de inventario con filtros
 */
export async function fetchEntradasInventario(
  params: { 
    inventario_id?: number;
    proveedor_id?: number;
    cliente_id?: number;
    lote_id?: number;
    estado_entrada_id?: number;
    fecha_desde?: string;
    fecha_hasta?: string;
    per_page?: number;
    page?: number;
  } = {}
): Promise<Paginated<EntradaInventario>> {
  const { data } = await http.get('/inventario/entradas-producto', { params });
  return data as Paginated<EntradaInventario>;
}

/**
 * Obtiene una entrada específica por ID
 */
export async function fetchEntradaInventario(id: number): Promise<EntradaInventario> {
  const { data } = await http.get(`/inventario/entradas-producto/${id}`);
  return (data?.data ?? data) as EntradaInventario;
}

/**
 * Actualiza el estado de una entrada
 */
export async function updateEstadoEntrada(
  id: number, 
  payload: UpdateEstadoEntradaPayload
): Promise<EntradaInventario> {
  const { data } = await http.patch(`/inventario/entradas-producto/${id}/estado`, payload);
  return (data?.data ?? data) as EntradaInventario;
}

/**
 * 🆕 Asignar o actualizar lote en una entrada con distribución de flete
 */
export async function asignarLoteEntrada(
  entradaId: number,
  payload: AsignarLotePayload
): Promise<EntradaInventario> {
  const { data } = await http.patch(`/inventario/entradas-producto/${entradaId}/lote`, payload);
  return (data?.data ?? data) as EntradaInventario;
}

/**
 * Elimina una entrada de inventario (reversa stock automáticamente)
 */
export async function deleteEntradaInventario(id: number): Promise<void> {
  await http.delete(`/inventario/entradas-producto/${id}`);
}

/**
 * Busca proveedores por NIT, nombre o contacto (búsqueda en vivo)
 */
export async function buscarProveedores(query: string): Promise<Proveedor[]> {
  if (!query || query.length < 2) {
    return [];
  }
  const { data } = await http.get('/inventario/proveedores/buscar', {
    params: { q: query }
  });
  return (data?.data ?? data ?? []) as Proveedor[];
}

/**
 * Busca clientes (para entradas tipo cliente)
 */
export async function buscarClientes(query: string): Promise<Cliente[]> {
  if (!query || query.length < 2) {
    return [];
  }
  const { data } = await http.get('/inventario/clientes/buscar', {
    params: { q: query }
  });
  return (data?.data ?? data ?? []) as Cliente[];
}

/**
 * Obtiene los estados de entrada disponibles
 */
export async function fetchEstadosEntrada(): Promise<EstadoEntrada[]> {
  const { data } = await http.get('/parametros/estados-entrada/options');
  return data as EstadoEntrada[];
}

// ============================================
// 🆕 GESTIÓN DE LOTES
// ============================================

/**
 * Obtiene todos los lotes con filtros
 */
export async function fetchLotes(
  params: {
    proveedor_id?: number;
    fecha_desde?: string;
    fecha_hasta?: string;
    per_page?: number;
  } = {}
): Promise<Paginated<Lote>> {
  const { data } = await http.get('/inventario/lotes', { params });
  return data as Paginated<Lote>;
}

/**
 * Obtiene lotes para selector (sin paginación)
 */
export async function fetchLotesOptions(proveedorId?: number): Promise<Lote[]> {
  const params = proveedorId ? { proveedor_id: proveedorId } : {};
  const { data } = await http.get('/inventario/lotes/options', { params });
  return (data?.data ?? data ?? []) as Lote[];
}

/**
 * Crea un nuevo lote
 */
export async function createLote(payload: CreateLotePayload): Promise<Lote> {
  const { data } = await http.post('/inventario/lotes', payload);
  return (data?.data ?? data) as Lote;
}

/**
 * Obtiene un lote específico
 */
export async function fetchLote(id: number): Promise<Lote> {
  const { data } = await http.get(`/inventario/lotes/${id}`);
  return (data?.data ?? data) as Lote;
}

/**
 * Actualiza un lote
 */
export async function updateLote(id: number, payload: Partial<CreateLotePayload>): Promise<Lote> {
  const { data } = await http.put(`/inventario/lotes/${id}`, payload);
  return (data?.data ?? data) as Lote;
}