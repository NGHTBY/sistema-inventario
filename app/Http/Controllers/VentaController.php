<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Producto;
use App\Models\VentaItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = Venta::with('detalles.producto')->latest()->get();
        
        // Estadísticas
        $totalVentas = Venta::count();
        $totalIngresos = Venta::sum('total');
        $ventasHoy = Venta::whereDate('fecha', today())->count();
        
        return view('ventas.index', compact('ventas', 'totalVentas', 'totalIngresos', 'ventasHoy'));
    }

    public function create()
    {
        $productos = Producto::where('stock', '>', 0)->where('activo', true)->get();
        return view('ventas.create', compact('productos'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            // DEBUG: Ver qué datos llegan
            Log::info('Datos recibidos en store:', $request->all());

            $request->validate([
                'cliente' => 'required|string|max:255',
                'productos' => 'required|array|min:1',
                'productos.*.id' => 'required|exists:productos,id',
                'productos.*.cantidad' => 'required|integer|min:1',
                'productos.*.precio' => 'required|numeric|min:0'
            ]);

            // Crear venta
            $venta = Venta::create([
                'factura' => 'FAC-' . str_pad(Venta::count() + 1, 6, '0', STR_PAD_LEFT),
                'total' => 0,
                'fecha' => now()
            ]);

            Log::info("Venta creada ID: {$venta->id}");

            $total = 0;

            // Procesar items de venta
            foreach ($request->productos as $index => $item) {
                Log::info("Procesando producto {$index}:", $item);
                
                $producto = Producto::find($item['id']);
                
                if (!$producto) {
                    throw new \Exception("Producto con ID {$item['id']} no encontrado");
                }

                // Verificar stock disponible
                if ($producto->stock < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para: {$producto->nombre}. Stock disponible: {$producto->stock}");
                }

                // Calcular subtotal
                $subtotal = $item['cantidad'] * $item['precio'];
                
                // Crear item de venta - CORREGIDO: Usar 'precio' consistentemente
                $ventaItem = VentaItem::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio'], // ← CORREGIDO: Se mantiene 'precio'
                    'subtotal' => $subtotal
                ]);

                Log::info("VentaItem creado ID: {$ventaItem->id} - Precio: {$item['precio']} - Subtotal: {$subtotal}");

                // Actualizar stock
                $producto->decrement('stock', $item['cantidad']);

                $total += $subtotal;
            }

            // Actualizar total de venta
            $venta->update(['total' => $total]);
            
            Log::info("Venta completada. Total: {$total}");

            DB::commit();

            return redirect()->route('ventas.show', $venta->id)
                ->with('success', 'Venta registrada exitosamente. Número de factura: ' . $venta->factura);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en venta: ' . $e->getMessage());
            return back()->with('error', 'Error al registrar venta: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $venta = Venta::with('detalles.producto')->findOrFail($id);
        return view('ventas.show', compact('venta'));
    }

    public function edit($id)
    {
        return redirect()->route('ventas.index')
            ->with('info', 'La edición de ventas no está permitida');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('ventas.index')
            ->with('info', 'La edición de ventas no está permitida');
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $venta = Venta::with('detalles.producto')->findOrFail($id);
            
            // Restaurar stock
            foreach ($venta->detalles as $detalle) {
                $producto = $detalle->producto;
                if ($producto) {
                    $producto->increment('stock', $detalle->cantidad);
                }
            }

            // Eliminar venta y detalles
            $venta->detalles()->delete();
            $venta->delete();

            DB::commit();

            return redirect()->route('ventas.index')
                ->with('success', 'Venta eliminada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar venta: ' . $e->getMessage());
        }
    }

    public function generarPDF($id)
    {
        try {
            $venta = Venta::with(['detalles.producto'])->findOrFail($id);
            
            Log::info("Generando PDF para venta ID: {$id}");
            Log::info("Detalles count: " . $venta->detalles->count());

            $pdf = Pdf::loadView('ventas.pdf', compact('venta'));
            return $pdf->download("factura_{$venta->factura}.pdf");
            
        } catch (\Exception $e) {
            Log::error('Error generando PDF: ' . $e->getMessage());
            return redirect()->route('ventas.show', $id)
                ->with('error', 'Error al generar PDF: ' . $e->getMessage());
        }
    }

    public function productosMasVendidos()
    {
        $productosMasVendidos = VentaItem::selectRaw('
                producto_id, 
                SUM(cantidad) as total_vendido, 
                SUM(subtotal) as total_generado,
                AVG(precio) as precio_promedio
            ')
            ->with('producto')
            ->groupBy('producto_id')
            ->orderByDesc('total_vendido')
            ->get();

        // DEBUG: Verificar datos
        Log::info('Productos más vendidos:', [
            'count' => $productosMasVendidos->count(),
            'data' => $productosMasVendidos->map(function($item) {
                return [
                    'producto' => $item->producto->nombre ?? 'N/A',
                    'total_vendido' => $item->total_vendido,
                    'total_generado' => $item->total_generado,
                    'precio_promedio' => $item->precio_promedio
                ];
            })->toArray()
        ]);

        return view('ventas.mas_vendidos', compact('productosMasVendidos'));
    }

    public function productosMasVendidosPDF()
    {
        $productos = VentaItem::selectRaw('
                producto_id, 
                SUM(cantidad) as total_vendido, 
                SUM(subtotal) as total_generado,
                AVG(precio) as precio_promedio
            ')
            ->with('producto')
            ->groupBy('producto_id')
            ->orderByDesc('total_vendido')
            ->get();

        // DEBUG: Verificar datos antes de generar PDF
        Log::info('Datos para PDF - Productos más vendidos:', [
            'count' => $productos->count(),
            'total_generado_sum' => $productos->sum('total_generado')
        ]);

        $pdf = Pdf::loadView('ventas.mas_vendidos_pdf', compact('productos'));
        return $pdf->download('productos_mas_vendidos.pdf');
    }
}