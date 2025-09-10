// src/features/OrdenServicio/api/proveedores.ts
import http from '../../../shared/api/http';

// Tipo
export interface Proveedor {
  id: number
  nombre: string
  contacto?: string
  telefono?: string
  correo?: string
  direccion?: string
  creado_en?: string
  actualizado_en?: string
}

// API
export async function fetchProveedores(): Promise<Proveedor[]> {
  const res = await http.get('/inventario/proveedores')
  return res.data.data // 👈 aquí está el array real según tu JSON
}
