<?php

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\CategoriaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('productos.index');
});

// Rutas para Productos
Route::resource('productos', ProductoController::class);
Route::get('productos/{producto}/historial-precios', [ProductoController::class, 'historialPrecios'])->name('productos.historial-precios');
Route::post('productos/{producto}/actualizar-precio', [ProductoController::class, 'actualizarPrecio'])->name('productos.actualizar-precio');
Route::delete('productos/{producto}/eliminar-historial/{historial}', [ProductoController::class, 'eliminarRegistroHistorial'])->name('productos.eliminar-historial');
Route::get('productos/pdf/generar', [ProductoController::class, 'generarPDF'])->name('productos.pdf');

// Rutas para Proveedores
Route::resource('proveedores', ProveedorController::class);
Route::get('proveedores/pdf/generar', [ProveedorController::class, 'pdf'])->name('proveedores.pdf'); // NUEVA RUTA AGREGADA

// Rutas para Categorías
Route::resource('categorias', CategoriaController::class);

// Rutas para Ventas
Route::resource('ventas', VentaController::class);
Route::get('ventas/{venta}/pdf', [VentaController::class, 'generarPDF'])->name('ventas.pdf');
Route::get('ventas/reporte/mas-vendidos', [VentaController::class, 'productosMasVendidos'])->name('ventas.mas-vendidos');
Route::get('ventas/reporte/mas-vendidos-pdf', [VentaController::class, 'productosMasVendidosPDF'])->name('ventas.mas-vendidos-pdf');

// Rutas para Reportes
Route::prefix('reportes')->group(function () {
    Route::get('productos-mas-vendidos', [ReporteController::class, 'productosMasVendidos'])->name('reportes.productos-mas-vendidos');
    Route::get('productos-mas-vendidos-pdf', [ReporteController::class, 'productosMasVendidosPDF'])->name('reportes.productos-mas-vendidos-pdf');
    Route::get('stock-bajo', [ReporteController::class, 'stockBajo'])->name('reportes.stock-bajo');
    Route::get('stock-bajo-pdf', [ReporteController::class, 'stockBajoPDF'])->name('reportes.stock-bajo-pdf');
    Route::get('ventas-periodo', [ReporteController::class, 'ventasPorPeriodo'])->name('reportes.ventas-periodo');
    Route::get('ventas-periodo-pdf', [ReporteController::class, 'ventasPorPeriodoPDF'])->name('reportes.ventas-periodo-pdf');
});

// Ruta adicional para compatibilidad
Route::get('ventas/reporte/mas_vendidos', function () {
    return redirect()->route('ventas.mas-vendidos');
})->name('ventas.reporte.mas_vendidos');