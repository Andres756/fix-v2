<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────
// Login
use App\Http\Controllers\Auth\RegisteredUserController;      
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// ─────────────────────────────────────────────
// Clientes
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\TecnicoController;

// ─────────────────────────────────────────────
// Parámetros
use App\Http\Controllers\Api\Parametros\Inventario\EstadosInventarioController;
use App\Http\Controllers\Api\Parametros\Inventario\TiposDeInventarioController;
use App\Http\Controllers\Api\Parametros\Inventario\MotivosIngresoController;

use App\Http\Controllers\Api\Parametros\OrdenServicio\EstadosOrdenServicioController;
use App\Http\Controllers\Api\Parametros\OrdenServicio\TiposTrabajoController;

use App\Http\Controllers\Api\Parametros\Facturacion\EstadosFacturaController;
use App\Http\Controllers\Api\Parametros\Facturacion\TiposItemFacturaController;
use App\Http\Controllers\Api\Parametros\Facturacion\AccionesFacturaLogController;
use App\Http\Controllers\Api\Parametros\Facturacion\ParametrosFacturacionController;
use App\Http\Controllers\Api\Parametros\Facturacion\TiposDescuentoController;
use App\Http\Controllers\Api\Parametros\Facturacion\AplicacionDescuentoController;
use App\Http\Controllers\Api\Parametros\Facturacion\FormasPagoController;
use App\Http\Controllers\Api\Parametros\Facturacion\TiposVentaController;


// ─────────────────────────────────────────────
// Inventario
use App\Http\Controllers\Api\Inventario\CategoriasController;
use App\Http\Controllers\Api\Inventario\ProveedoresController;
use App\Http\Controllers\Api\Inventario\LotesController;
use App\Http\Controllers\Api\Inventario\InventariosController;
use App\Http\Controllers\Api\Inventario\EntradasProductoController;
use App\Http\Controllers\Api\Inventario\DetallesProductoController;
use App\Http\Controllers\Api\Inventario\DetallesRepuestoController;

// ─────────────────────────────────────────────
// Orden de Servicio
use App\Http\Controllers\Api\OrdenServicio\OrdenServicioController;
use App\Http\Controllers\Api\OrdenServicio\EquipoOrdenServicioController;
use App\Http\Controllers\Api\OrdenServicio\TareaEquipoController;
use App\Http\Controllers\Api\OrdenServicio\RepuestoOsInventarioController;
use App\Http\Controllers\Api\OrdenServicio\RepuestoOsExternoController;
use App\Http\Controllers\Api\OrdenServicio\HistorialEstadoOsController;


// ─────────────────────────────────────────────
// Auth
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login',    [AuthenticatedSessionController::class, 'store']);
Route::post('/logout',   [AuthenticatedSessionController::class, 'destroy'])->middleware('auth:sanctum');

// ── Parámetros
Route::prefix('parametros')->group(function () {
    // Inventario
    Route::get('estados-inventario/options', [EstadosInventarioController::class, 'options']);
    Route::apiResource('estados-inventario', EstadosInventarioController::class)
        ->parameters(['estados-inventario' => 'estado_inventario'])->except(['destroy']);

    Route::get('tipos-de-inventario/options', [TiposDeInventarioController::class, 'options']);
    Route::apiResource('tipos-de-inventario', TiposDeInventarioController::class)
        ->parameters(['tipos-de-inventario' => 'tipo_de_inventario'])->except(['destroy']);

    Route::get('motivos-ingreso/options', [MotivosIngresoController::class, 'options']);
    Route::apiResource('motivos-ingreso', MotivosIngresoController::class)
        ->parameters(['motivos-ingreso' => 'motivo_ingreso'])->except(['destroy']);

    // Facturación
    Route::get('formas-pago/options', [FormasPagoController::class, 'options']);
    Route::apiResource('formas-pago', FormasPagoController::class)
        ->parameters(['formas-pago' => 'forma_pago'])->except(['destroy']);

    Route::get('tipos-venta/options', [TiposVentaController::class, 'options']);
    Route::apiResource('tipos-venta', TiposVentaController::class)
        ->parameters(['tipos-venta' => 'tipo_venta'])->except(['destroy']);

    Route::get('tipos-descuento/options', [TiposDescuentoController::class, 'options']);
    Route::apiResource('tipos-descuento', TiposDescuentoController::class)
        ->parameters(['tipos-descuento' => 'tipo_descuento'])->except(['destroy']);

    Route::get('acciones-factura-log/options', [AccionesFacturaLogController::class, 'options']);
    Route::apiResource('acciones-factura-log', AccionesFacturaLogController::class)
        ->parameters(['acciones-factura-log' => 'accion_factura_log'])->except(['destroy']);

    Route::get('aplicacion-descuento/options', [AplicacionDescuentoController::class, 'options']);
    Route::apiResource('aplicacion-descuento', AplicacionDescuentoController::class)
        ->parameters(['aplicacion-descuento' => 'aplicacion_descuento'])->except(['destroy']);

    Route::get('estados-factura/options', [EstadosFacturaController::class, 'options']);
    Route::apiResource('estados-factura', EstadosFacturaController::class)
        ->parameters(['estados-factura' => 'estado_factura'])->except(['destroy']);

});

// ── Inventario & Compras
Route::prefix('inventario')->group(function () {
    Route::apiResource('categorias', CategoriasController::class)
        ->parameters(['categorias' => 'categoria']);
    Route::apiResource('proveedores', ProveedoresController::class)
        ->parameters(['proveedores' => 'proveedor']);
    Route::apiResource('lotes', LotesController::class)
        ->parameters(['lotes' => 'lote']);
    Route::apiResource('inventarios', InventariosController::class)
        ->parameters(['inventarios' => 'inventario']);
    Route::apiResource('entradas-producto', EntradasProductoController::class)
        ->parameters(['entradas-producto' => 'entradas_producto']);
});

// ── Busquedad repuestos
Route::get('inventario/repuestos/search', [InventariosController::class, 'searchRepuestos']);

// Tecnicos
Route::get('tecnicos', [TecnicoController::class, 'index']);
Route::get('/tecnicos/{id}/equipos', [TecnicoController::class, 'equiposAsignados']);
Route::get('/tecnicos/{id}/ganancias', [TecnicoController::class, 'ganancias']);
Route::put('/tecnicos/{id}/tareas/{tareaId}/estado', [TecnicoController::class, 'actualizarEstadoTarea']);
Route::get('/tecnicos/{id}/tareas/{tareaId}/historial', [TecnicoController::class, 'historialTarea']);


// OS
Route::get('estados-orden-servicio/options', [EstadosOrdenServicioController::class, 'options']);
Route::apiResource('estados-orden-servicio', EstadosOrdenServicioController::class)
    ->parameters(['estados-orden-servicio' => 'estado_orden_servicio'])
    ->except(['destroy']);

Route::get('tipos-trabajo/options', [TiposTrabajoController::class, 'options']);
Route::apiResource('tipos-trabajo', TiposTrabajoController::class)
    ->parameters(['tipos-trabajo' => 'tipo_trabajo'])
    ->except(['destroy']);

// Listado global de órdenes (sin quitar lo anidado)
Route::get('ordenes', [OrdenServicioController::class, 'listAll']);

// Calcular costos
Route::get('equipos/{equipoId}/costos', [EquipoOrdenServicioController::class, 'costos']);

// ── Clientes
Route::apiResource('clientes', ClienteController::class)
    ->parameters(['clientes' => 'cliente']);

// ── Órdenes de un Cliente
Route::prefix('clientes/{clienteId}/ordenes')->group(function () {
    Route::get('/', [OrdenServicioController::class, 'index']);
    Route::post('/', [OrdenServicioController::class, 'store']);
    Route::get('{ordenId}', [OrdenServicioController::class, 'show']);
    Route::put('{ordenId}', [OrdenServicioController::class, 'update']);
    Route::delete('{ordenId}', [OrdenServicioController::class, 'destroy']);

    // ── Equipos de una Orden
    Route::prefix('{ordenId}/equipos')->group(function () {
        Route::get('/', [EquipoOrdenServicioController::class, 'index']);
        Route::post('/', [EquipoOrdenServicioController::class, 'store']);
        Route::get('{equipoId}', [EquipoOrdenServicioController::class, 'show']);
        Route::put('{equipoId}', [EquipoOrdenServicioController::class, 'update']);
        Route::delete('{equipoId}', [EquipoOrdenServicioController::class, 'destroy']);

        // ── Tareas de un Equipo
        Route::prefix('{equipoId}/tareas')->group(function () {
            Route::get('/', [TareaEquipoController::class, 'index']);
            Route::post('/', [TareaEquipoController::class, 'store']);
            Route::get('{tareaId}', [TareaEquipoController::class, 'show']);
            Route::put('{tareaId}', [TareaEquipoController::class, 'update']);
            Route::delete('{tareaId}', [TareaEquipoController::class, 'destroy']);
        });

        // ── Repuestos Inventario
        Route::prefix('{equipoId}/repuestos-inventario')->group(function () {
            Route::get('/', [RepuestoOsInventarioController::class, 'index']);
            Route::post('/', [RepuestoOsInventarioController::class, 'store']);
            Route::get('{repuestoId}', [RepuestoOsInventarioController::class, 'show']);
            Route::put('{repuestoId}', [RepuestoOsInventarioController::class, 'update']);
            Route::delete('{repuestoId}', [RepuestoOsInventarioController::class, 'destroy']);
        });

        // ── Repuestos Externos
        Route::prefix('{equipoId}/repuestos-externos')->group(function () {
            Route::get('/', [RepuestoOsExternoController::class, 'index']);
            Route::post('/', [RepuestoOsExternoController::class, 'store']);
            Route::get('{repuestoExternoId}', [RepuestoOsExternoController::class, 'show']);
            Route::put('{repuestoExternoId}', [RepuestoOsExternoController::class, 'update']);
            Route::delete('{repuestoExternoId}', [RepuestoOsExternoController::class, 'destroy']);
        });

        // ── Historial de un Equipo
        Route::get('{equipoId}/historial', [HistorialEstadoOsController::class, 'equipoHistorial']);
    });

    // Historial de una Orden
    Route::get('{ordenId}/historial', [HistorialEstadoOsController::class, 'ordenHistorial']);
});

Route::get('debug-simple', function() {
    return response()->json(['message' => 'API funcionando correctamente']);
});

// ── Ruta protegida de ejemplo
Route::middleware('auth:sanctum')->get('/user', fn (Request $request) => $request->user());
