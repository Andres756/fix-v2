import http from '../../../shared/api/http';
import type { Paginated } from '../../../shared/types/pagination';
import type { Cliente } from '../types/cliente';

/* ====== LISTAR / PAGINAR ====== */
export interface FetchClientesParams {
  q?: string;
  page?: number;
  per_page?: number;
}

export async function fetchClientes(
  params: FetchClientesParams = {}
): Promise<Paginated<Cliente>> {
  const { data } = await http.get('/clientes', { params });
  return data as Paginated<Cliente>;
}

/* ====== OBTENER UNO ====== */
export async function fetchClienteById(id: number): Promise<Cliente> {
  const { data } = await http.get(`/clientes/${id}`);
  return (data?.data ?? data) as Cliente;
}

/* ====== CREAR ====== */
export interface CreateClientePayload {
  nombre: string;
  documento: string;
  telefono?: string | null;
  correo?: string | null;
  direccion?: string | null;
}

export async function createCliente(payload: CreateClientePayload): Promise<Cliente> {
  const { data } = await http.post('/clientes', payload);
  return (data?.data ?? data) as Cliente;
}

/* ====== ACTUALIZAR ====== */
export type UpdateClientePayload = Partial<CreateClientePayload>;

export async function updateCliente(id: number, payload: UpdateClientePayload): Promise<Cliente> {
  const { data } = await http.put(`/clientes/${id}`, payload);
  return (data?.data ?? data) as Cliente;
}
