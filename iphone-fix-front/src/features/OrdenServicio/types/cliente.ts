export interface Cliente {
  id: number;
  nombre: string;
  documento: string;
  telefono?: string | null;
  correo?: string | null;
  direccion?: string | null;
  created_at?: string;
  updated_at?: string;
}
