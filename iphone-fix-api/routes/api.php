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
use App\Http\Controllers\Api\Inventario\SalidasProductoController;
use App\Http\Controllers\Api\Inventario\DetallesProductoController;
use App\Http\Controllers\Api\Inventario\DetallesRepuestoController;
use App\Http\Controllers\api\Inventario\InventarioExportController;
use App\Http\Controllers\Api\Inventario\ModelosEquiposController;
use App\Http\Controllers\Api\Inventario\EstadosEntradaController;

// ─────────────────────────────────────────────
// Plan Separe
use App\Http\Controllers\Api\PlanSepare\PlanSepareController;
use App\Http\Controllers\Api\PlanSepare\AbonoController;
use App\Http\Controllers\Api\PlanSepare\EstadoController;
use App\Http\Controllers\Api\PlanSepare\MotivoAnulacionPlanSepareController;

// ─────────────────────────────────────────────
// Facturacion
use App\Http\Controllers\Api\Facturacion\{FacturacionController,PagosFacturaController};

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

    Route::get('estados-entrada', [EstadosEntradaController::class, 'index']);
    Route::get('estados-entrada/options', [EstadosEntradaController::class, 'options']);
    Route::get('estados-entrada/{id}', [EstadosEntradaController::class, 'show']);

});

// ── Inventario & Compras
Route::prefix('inventario')->group(function () {
    // 📦 Categorías
    Route::apiResource('categorias', CategoriasController::class)
        ->parameters(['categorias' => 'categoria']);

    // Búsqueda de proveedores en vivo
    Route::get('proveedores/buscar', [EntradasProductoController::class, 'buscarProveedores']);
    
    // 🧾 Proveedores
    Route::apiResource('proveedores', ProveedoresController::class)
        ->parameters(['proveedores' => 'proveedor']);

    Route::get('lotes/options', [LotesController::class, 'options']);
    
    // 📦 Lotes
    Route::apiResource('lotes', LotesController::class)
        ->parameters(['lotes' => 'lote']);
    
    // 🏷️ Inventarios
    Route::apiResource('inventarios', InventariosController::class)
        ->parameters(['inventarios' => 'inventario']);

    Route::patch('entradas-producto/{id}/lote', [EntradasProductoController::class, 'asignarLote']);
    
    // 📥 Entradas de producto
    Route::apiResource('entradas-producto', EntradasProductoController::class)
        ->parameters(['entradas-producto' => 'entradas_producto']);
    
    // 📤 Salidas de producto
    Route::apiResource('salidas-producto', SalidasProductoController::class)
        ->parameters(['salidas-producto' => 'salidas_producto']);
    
    // 📊 Exportar inventario a Excel
    Route::get('exportar', [InventarioExportController::class, 'exportar'])
        ->name('inventario.exportar');

        // ✅ NUEVAS RUTAS: Modelos de Equipos
    Route::get('modelos-equipos/options', [ModelosEquiposController::class, 'options']);
    Route::get('modelos-equipos/reporte', [ModelosEquiposController::class, 'reporteInventario']);
    Route::get('modelos-equipos/{modeloEquipo}/equipos', [ModelosEquiposController::class, 'equiposPorModelo']);
    Route::apiResource('modelos-equipos', ModelosEquiposController::class)
        ->parameters(['modelos-equipos' => 'modeloEquipo']);

        // Entradas de producto
    Route::get('entradas-producto', [EntradasProductoController::class, 'index']);
    Route::post('entradas-producto', [EntradasProductoController::class, 'store']);
    Route::get('entradas-producto/{id}', [EntradasProductoController::class, 'show']);
    Route::patch('entradas-producto/{id}/estado', [EntradasProductoController::class, 'updateEstado']);
    Route::delete('entradas-producto/{id}', [EntradasProductoController::class, 'destroy']);
    
    // Búsqueda de clientes en vivo
    Route::get('clientes/buscar', [EntradasProductoController::class, 'buscarClientes']);
});

// ── Plan Separe
Route::prefix('plan-separe')->middleware(['auth:sanctum'])->group(function () {

    Route::get('/motivos-anulacion', [MotivoAnulacionPlanSepareController::class, 'index']);

    Route::get('/', [PlanSepareController::class, 'index']);
    Route::post('/', [PlanSepareController::class, 'store']);
    Route::get('/{id}', [PlanSepareController::class, 'show']);
    Route::patch('/{id}/anular', [PlanSepareController::class, 'anular']);

    // Abonos
    Route::get('/{id}/abonos', [AbonoController::class, 'index']);
    Route::post('/{id}/abonos', [AbonoController::class, 'store']);

    // Devoluciones
    // Route::get('/{id}/devoluciones', [DevolucionController::class, 'index']);
    // Route::post('/{id}/devoluciones', [DevolucionController::class, 'store']);

    Route::patch('/{id}/reasignar', [PlanSepareController::class, 'reasignar']);

    // Estado manual
    Route::patch('/{id}/estado', [EstadoController::class, 'update']);
    Route::patch('/{id}/estado/devolver', [EstadoController::class, 'devolver']);
    
});


// ── FACTURACION
Route::prefix('facturacion')->middleware(['auth:sanctum'])->group(function () {

    // 📊 Facturas
    Route::get('facturas', [FacturacionController::class, 'index']);              // Listar facturas (vista resumen)
    Route::get('facturas/{id}', [FacturacionController::class, 'show']);          // Mostrar una factura con detalle
    Route::post('facturas', [FacturacionController::class, 'store']);             // Crear nueva factura (venta o servicio)
    Route::patch('facturas/{id}/anular', [FacturacionController::class, 'anular']); // Anular factura
    Route::patch('facturas/{id}/entregar', [FacturacionController::class, 'entregar']);
    Route::patch('facturas/{id}/equiposentrega', [FacturacionController::class, 'entregarEquipos']);
    // 📦 Anulación avanzada (productos o equipos)
    Route::patch('facturas/{id}/anular-avanzado', [FacturacionController::class, 'anularAvanzado']);
    Route::get('facturas/{id}/verificar-anulacion', [FacturacionController::class, 'verificarAnulacion']);

    // 💰 Pagos asociados a factura
    Route::get('facturas/{id}/pagos', [PagosFacturaController::class, 'index']);  // Listar pagos de factura
    Route::post('facturas/{id}/pagos', [PagosFacturaController::class, 'store']); // Registrar pago o abono
    Route::put('pagos/{id}/anular', [PagosFacturaController::class, 'anular']); // ✅ plural aquí
    Route::get('pagos/motivos-anulacion', [PagosFacturaController::class, 'motivosAnulacion']);

    Route::get('facturas/{id}/ticket', [FacturacionController::class, 'generarTicket']) ->middleware('auth:sanctum');
    Route::get('facturacion/facturas/{id}/imprimir', [FacturacionController::class, 'obtenerUrlImpresion']) ->middleware('auth:sanctum');

    Route::get('resumen', [FacturacionController::class, 'resumen']);
    Route::post('ordenes/{orden}/prefacturar', [FacturacionController::class, 'prefacturarOS']);
});

// ── Busquedad repuestos
Route::get('inventario/repuestos/search', [InventariosController::class, 'searchRepuestos']);

// Tecnicos
Route::get('tecnicos', [TecnicoController::class, 'index']);
Route::get('/tecnicos/{id}/dashboard', [TecnicoController::class, 'dashboard']); // ✅ NUEVA
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

Route::patch('equipos/{equipoId}/estado', [EquipoOrdenServicioController::class, 'actualizarEstado']);



// ── Clientes
Route::apiResource('clientes', ClienteController::class)
    ->parameters(['clientes' => 'cliente']);

// ── Órdenes de un Cliente
Route::prefix('clientes/{clienteId}/ordenes')
    ->middleware(['auth:sanctum']) // 👈 asegura que Auth::id() devuelva el usuario correcto
    ->group(function () {

        // ── CRUD principal
        Route::get('/', [OrdenServicioController::class, 'index']);
        Route::post('/', [OrdenServicioController::class, 'store']);
        Route::get('{ordenId}', [OrdenServicioController::class, 'show']);
        Route::put('{ordenId}', [OrdenServicioController::class, 'update']);
        Route::delete('{ordenId}', [OrdenServicioController::class, 'destroy']);

        // ── Actualizar estado de una Orden
        Route::patch('{ordenId}/actualizar-estado', [OrdenServicioController::class, 'actualizarEstado']);

        // ── Equipos de una Orden
        Route::prefix('{ordenId}/equipos')->group(function () {

            // CRUD de equipos
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

            // ── Repuestos de Inventario
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

        // ── Historial de una Orden
        Route::get('{ordenId}/historial', [HistorialEstadoOsController::class, 'ordenHistorial']);
    });

Route::get('debug-simple', function() {
    return response()->json(['message' => 'API funcionando correctamente']);
});

// ── Ruta protegida de ejemplo
Route::middleware('auth:sanctum')->get('/user', fn (Request $request) => $request->user());
