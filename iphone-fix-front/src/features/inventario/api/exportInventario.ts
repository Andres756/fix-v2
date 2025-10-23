// src/features/inventario/api/exportInventario.ts
import axios from "axios";
import type { InventarioExportFilters } from "../types/exportInventario";
import { toast } from 'vue3-toastify';

export async function exportarInventario(filters: InventarioExportFilters) {
  try {
    // Limpiar filtros vacíos
    const cleanFilters = Object.fromEntries(
      Object.entries(filters).filter(([_, value]) => value !== "" && value !== null && value !== undefined)
    );
    
    const { data } = await axios.get(
      `${import.meta.env.VITE_API_BASE_URL}/inventario/exportar`,
      {
        params: cleanFilters, // Enviar filtros como query parameters
        responseType: "blob",
      }
    );
    
    // Verificar si la respuesta es realmente un archivo
    if (data.type === 'application/json') {
      const text = await data.text();
      const error = JSON.parse(text);
      throw new Error(error.message || 'Error al generar el reporte');
    }
    
    let tipoNombre = "inventario";
    if (filters.tipo_inventario_nombre) {
      tipoNombre = filters.tipo_inventario_nombre
        .toLowerCase()
        .replace(/\s+/g, "_");
    }
    
    const fecha = new Date().toISOString().split("T")[0];
    const nombreArchivo = `reporte_${tipoNombre}_${fecha}.xlsx`;
    
    const url = window.URL.createObjectURL(new Blob([data]));
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute("download", nombreArchivo);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
    
    toast.success('Reporte generado exitosamente');
  } catch (error: any) {
    console.error("Error exportando inventario:", error);
    
    if (error.response?.data) {
      const text = await error.response.data.text();
      try {
        const errorData = JSON.parse(text);
        toast.error(errorData.message || 'Error al generar el reporte');
      } catch {
        toast.error('Error al generar el reporte');
      }
    } else {
      toast.error(error.message || 'Error al generar el reporte');
    }
    
    throw error;
  }
}

export async function fetchTiposInventario() {
  try {
    const { data } = await axios.get(
      `${import.meta.env.VITE_API_BASE_URL}/parametros/tipos-de-inventario/options`
    );
    
    if (typeof data === 'string') {
      throw new Error("Respuesta inválida del servidor");
    }
    
    return Array.isArray(data) ? data : data.data || [];
  } catch (error) {
    console.error("Error al cargar tipos de inventario:", error);
    toast.error('Error al cargar los tipos de inventario');
    throw error;
  }
}