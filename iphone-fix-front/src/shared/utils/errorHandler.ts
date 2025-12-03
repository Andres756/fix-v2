// iphone-fix-front/src/shared/utils/errorHandler.ts

/**
 * Extrae el mensaje de error más relevante de una respuesta de error del backend
 * Maneja diferentes formatos de error de Laravel/API
 */
export function extractErrorMessage(error: any): string {
  // Si no hay error, retornar mensaje genérico
  if (!error) {
    return 'Ha ocurrido un error inesperado'
  }

  // 1. Intentar extraer de error.response.data (formato Axios)
  const responseData = error?.response?.data

  if (responseData) {
    // 1a. Mensaje directo en 'message'
    if (responseData.message) {
      return responseData.message
    }

    // 1b. Mensaje en 'error'
    if (responseData.error) {
      return typeof responseData.error === 'string' 
        ? responseData.error 
        : JSON.stringify(responseData.error)
    }

    // 1c. Errores de validación en 'errors' (Laravel validation)
    if (responseData.errors && typeof responseData.errors === 'object') {
      const firstErrorKey = Object.keys(responseData.errors)[0]
      const firstError = responseData.errors[firstErrorKey]
      
      if (Array.isArray(firstError) && firstError.length > 0) {
        return firstError[0]
      }
      
      if (typeof firstError === 'string') {
        return firstError
      }
    }

    // 1d. Si errors es un string directo
    if (typeof responseData.errors === 'string') {
      return responseData.errors
    }
  }

  // 2. Intentar extraer de error.message (errores de JavaScript)
  if (error.message) {
    return error.message
  }

  // 3. Si es un string directo
  if (typeof error === 'string') {
    return error
  }

  // 4. Mensajes por código de estado HTTP
  if (error?.response?.status) {
    const statusMessages: Record<number, string> = {
      400: 'Solicitud incorrecta',
      401: 'No autorizado. Por favor, inicie sesión nuevamente',
      403: 'No tiene permisos para realizar esta acción',
      404: 'Recurso no encontrado',
      409: 'Conflicto con el estado actual del recurso',
      422: 'Error de validación en los datos enviados',
      500: 'Error interno del servidor',
      502: 'Error de comunicación con el servidor',
      503: 'Servicio no disponible temporalmente'
    }

    const statusMessage = statusMessages[error.response.status]
    if (statusMessage) {
      return statusMessage
    }
  }

  // 5. Fallback final
  return 'Ha ocurrido un error. Por favor, intente nuevamente'
}

/**
 * Extrae todos los errores de validación como un objeto
 * Útil para mostrar errores campo por campo
 */
export function extractValidationErrors(error: any): Record<string, string[]> {
  const responseData = error?.response?.data

  if (responseData?.errors && typeof responseData.errors === 'object') {
    return responseData.errors
  }

  return {}
}

/**
 * Verifica si un error es de tipo validación
 */
export function isValidationError(error: any): boolean {
  return error?.response?.status === 422
}

/**
 * Verifica si un error es de tipo duplicidad (UNIQUE constraint)
 */
export function isDuplicateError(error: any): boolean {
  const message = extractErrorMessage(error).toLowerCase()
  return (
    error?.response?.status === 409 ||
    message.includes('duplicate') ||
    message.includes('duplicado') ||
    message.includes('ya existe') ||
    message.includes('unique')
  )
}

/**
 * Formatea un mensaje de error para mostrar en toast
 * Limita la longitud y formatea para mejor legibilidad
 */
export function formatErrorForToast(error: any, maxLength = 200): string {
  let message = extractErrorMessage(error)

  // Limpiar mensajes SQL si existen
  if (message.includes('SQLSTATE')) {
    const match = message.match(/SQLSTATE\[.*?\]:\s*(.+?)(?:\s*\(SQL:|$)/i)
    if (match && match[1]) {
      message = match[1].trim()
    }
  }

  // Limitar longitud
  if (message.length > maxLength) {
    message = message.substring(0, maxLength) + '...'
  }

  return message
}