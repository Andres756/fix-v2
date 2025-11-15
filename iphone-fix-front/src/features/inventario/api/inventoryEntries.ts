// iphone-fix-front/src/features/inventario/api/inventoryEntries.ts

import http from '../../../shared/api/http';
import type { Paginated } from '../../../shared/types/pagination';
import type { 
  EntradaInventario, 
  CreateEntradaInventarioPayload,
  UpdateEstadoEntradaPayload,
  EstadoEntrada,
  Proveedor
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
    lote_id?: number;
    estado_entrada_id?: number;
    fecha_desde?: string;
    fecha_hasta?: string;
    per_page?: number;
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
 * Obtiene los estados de entrada disponibles
 */
export async function fetchEstadosEntrada(): Promise<EstadoEntrada[]> {
  const { data } = await http.get('/parametros/estados-entrada/options');
  return data as EstadoEntrada[];
}