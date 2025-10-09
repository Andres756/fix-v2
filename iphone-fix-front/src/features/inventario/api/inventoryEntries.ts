// iphone-fix-front/src/features/inventario/api/inventoryEntry.ts

import http from '../../../shared/api/http';
import type { Paginated } from '../../../shared/types/pagination';
import type { EntradaInventario, CreateEntradaInventarioPayload } from '../types/inventoryEntry';

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
 * Obtiene todas las entradas de inventario
 */
export async function fetchEntradasInventario(
  params: { inventario_id?: number; lote_id?: number; per_page?: number } = {}
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
 * Elimina una entrada de inventario
 */
export async function deleteEntradaInventario(id: number): Promise<void> {
  await http.delete(`/inventario/entradas-producto/${id}`);
}