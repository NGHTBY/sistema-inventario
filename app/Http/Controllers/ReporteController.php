<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function productosMasVendidos()
    {
        $productosMasVendidos = VentaDetalle::selectRaw('
                producto_id, 
                SUM(cantidad) as total_vendido, 
                SUM(subtotal) as total_ingresos,
                AVG(precio_unitario) as precio_promedio
            ')
            ->with('producto')
            ->groupBy('producto_id')
            ->orderByDesc('total_vendido')
            ->get();

        return view('reportes.productos_mas_vendidos', compact('productosMasVendidos'));
    }

    public function productosMasVendidosPDF()
    {
        $productosMasVendidos = VentaDetalle::selectRaw('
                producto_id, 
                SUM(cantidad) as total_vendido, 
                SUM(subtotal) as total_ingresos,
                AVG(precio_unitario) as precio_promedio
            ')
            ->with('producto')
            ->groupBy('producto_id')
            ->orderByDesc('total_vendido')
            ->get();

        $pdf = Pdf::loadView('reportes.productos_mas_vendidos_pdf', compact('productosMasVendidos'));
        return $pdf->download('productos_mas_vendidos.pdf');
    }

    public function stockBajo()
    {
        $productosStockBajo = Producto::with(['proveedor', 'categoria'])
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->where('stock', '>', 0)
            ->get();

        return view('reportes.stock_bajo', compact('productosStockBajo'));
    }

    public function stockBajoPDF()
    {
        $productosStockBajo = Producto::with(['proveedor', 'categoria'])
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->where('stock', '>', 0)
            ->get();

        $pdf = Pdf::loadView('reportes.stock_bajo_pdf', compact('productosStockBajo'));
        return $pdf->download('stock_bajo.pdf');
    }

    public function ventasPorPeriodo(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ?? now()->subMonth()->format('Y-m-d');
        $fechaFin = $request->fecha_fin ?? now()->format('Y-m-d');

        $ventas = Venta::with('detalles.producto')
            ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
            ->get();

        $totalVentas = $ventas->count();
        $totalIngresos = $ventas->sum('total');
        $ventaPromedio = $totalVentas > 0 ? $totalIngresos / $totalVentas : 0;

        return view('reportes.ventas_periodo', compact(
            'ventas', 
            'totalVentas', 
            'totalIngresos', 
            'ventaPromedio',
            'fechaInicio',
            'fechaFin'
        ));
    }

    public function ventasPorPeriodoPDF(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ?? now()->subMonth()->format('Y-m-d');
        $fechaFin = $request->fecha_fin ?? now()->format('Y-m-d');

        $ventas = Venta::with('detalles.producto')
            ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
            ->get();

        $totalVentas = $ventas->count();
        $totalIngresos = $ventas->sum('total');
        $ventaPromedio = $totalVentas > 0 ? $totalIngresos / $totalVentas : 0;

        $pdf = Pdf::loadView('reportes.ventas_periodo_pdf', compact(
            'ventas', 
            'totalVentas', 
            'totalIngresos', 
            'ventaPromedio',
            'fechaInicio',
            'fechaFin'
        ));

        return $pdf->download("ventas_{$fechaInicio}_a_{$fechaFin}.pdf");
    }
}