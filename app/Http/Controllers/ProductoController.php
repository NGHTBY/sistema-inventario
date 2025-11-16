<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\HistorialPrecio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        // Consulta base
        $query = Producto::with(['proveedor']);

        // Búsqueda por término
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nombre', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('codigo', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('categoria', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('descripcion', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('codigo_barras', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('proveedor', function($q) use ($searchTerm) {
                      $q->where('empresa', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('contacto', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }

        // Aplicar filtros de stock
        if ($request->has('stock')) {
            switch ($request->stock) {
                case 'bajo':
                    $query->whereRaw('stock <= stock_minimo AND stock > 0');
                    break;
                case 'agotado':
                    $query->where('stock', 0);
                    break;
                case 'alto':
                    $query->whereRaw('stock >= stock_maximo');
                    break;
            }
        }

        $productos = $query->latest()->paginate(10);
        
        // Estadísticas para los cards (siempre mostrar totales reales, no filtrados)
        $totalProductos = Producto::count();
        $productosActivos = Producto::where('activo', true)->count();
        $stockBajo = Producto::whereRaw('stock <= stock_minimo AND stock > 0')->count();
        $sinStock = Producto::where('stock', 0)->count();
        $stockAlto = Producto::whereRaw('stock >= stock_maximo')->count();
        
        return view('productos.index', compact('productos', 'totalProductos', 'productosActivos', 'stockBajo', 'sinStock', 'stockAlto'));
    }

    public function create()
    {
        $proveedores = Proveedor::where('activo', true)->get();
        return view('productos.create', compact('proveedores'));
    }

    public function store(Request $request)
    {
        Log::info('=== INICIANDO CREACIÓN DE PRODUCTO ===');
        Log::info('Datos del formulario:', $request->all());
        
        try {
            // Validación
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255',
                'categoria' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'precio_compra' => 'required|numeric|min:0',
                'precio_venta' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'stock_minimo' => 'required|integer|min:0',
                'stock_maximo' => 'required|integer|min:0',
                'proveedor_id' => 'nullable|exists:proveedores,id',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            Log::info('✅ Validación pasada correctamente');

            // Preparar datos para crear producto
            $productoData = [
                'codigo' => $request->codigo ?: 'PROD-' . strtoupper(uniqid()),
                'nombre' => $request->nombre,
                'categoria' => $request->categoria,
                'descripcion' => $request->descripcion,
                'precio_compra' => (float) $request->precio_compra,
                'precio_venta' => (float) $request->precio_venta,
                'stock' => (int) $request->stock,
                'stock_minimo' => (int) $request->stock_minimo,
                'stock_maximo' => (int) $request->stock_maximo,
                'proveedor_id' => $request->proveedor_id,
                'activo' => true
            ];

            // Manejar la subida de la imagen
            if ($request->hasFile('foto')) {
                Log::info('📸 Subiendo imagen...');
                $imagePath = $request->file('foto')->store('productos', 'public');
                $productoData['foto'] = $imagePath;
                Log::info('✅ Imagen guardada en: ' . $imagePath);
            }

            // CREAR PRODUCTO usando create() en lugar de save()
            $producto = Producto::create($productoData);
            Log::info('✅ Producto creado con ID: ' . $producto->id);

            // Registrar precio inicial en historial - CORREGIDO
            HistorialPrecio::create([
                'producto_id' => $producto->id,
                'precio_anterior' => 0.00, // CORREGIDO: 0 → 0.00
                'precio_nuevo' => $producto->precio_venta,
                'diferencia' => $producto->precio_venta,
                'porcentaje_cambio' => 100.00, // CORREGIDO: 100 → 100.00
                'motivo' => 'Precio inicial',
                'fecha_cambio' => now()
            ]);

            Log::info('✅ Historial de precio creado');

            // REDIRECCIÓN GARANTIZADA
            $mensaje = '✅ Producto "' . $producto->nombre . '" creado exitosamente. Código: ' . $producto->codigo;
            Log::info('🔄 Redirigiendo a productos.index');

            // Forzar la redirección de manera explícita
            return redirect()->to(route('productos.index'))->with('success', $mensaje);

        } catch (\Exception $e) {
            Log::error('❌ Error en store: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()
                ->with('error', '❌ Error al crear producto: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Producto $producto)
    {
        $producto->load(['proveedor', 'historialPrecios']);
        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        $proveedores = Proveedor::where('activo', true)->get();
        return view('productos.edit', compact('producto', 'proveedores'));
    }

    public function update(Request $request, Producto $producto)
    {
        Log::info('Datos del formulario edición:', $request->all());
        
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'categoria' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'precio_compra' => 'required|numeric|min:0',
                'precio_venta' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'stock_minimo' => 'required|integer|min:0',
                'stock_maximo' => 'required|integer|min:0',
                'proveedor_id' => 'nullable|exists:proveedores,id',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Guardar precio anterior para comparar
            $precioAnterior = $producto->precio_venta;

            // Actualizar campos individualmente
            $producto->codigo = $request->codigo;
            $producto->nombre = $request->nombre;
            $producto->categoria = $request->categoria;
            $producto->descripcion = $request->descripcion;
            $producto->precio_compra = (float) $request->precio_compra;
            $producto->precio_venta = (float) $request->precio_venta;
            $producto->stock = (int) $request->stock;
            $producto->stock_minimo = (int) $request->stock_minimo;
            $producto->stock_maximo = (int) $request->stock_maximo;
            $producto->proveedor_id = $request->proveedor_id;

            // Manejar la subida de la imagen
            if ($request->hasFile('foto')) {
                // Eliminar imagen anterior si existe
                if ($producto->foto) {
                    Storage::disk('public')->delete($producto->foto);
                }
                
                $imagePath = $request->file('foto')->store('productos', 'public');
                $producto->foto = $imagePath;
            }

            $producto->save();

            // Registrar cambio de precio si es diferente
            if ($precioAnterior != $producto->precio_venta) {
                $diferencia = $producto->precio_venta - $precioAnterior;
                $porcentaje = $precioAnterior > 0 ? (($diferencia / $precioAnterior) * 100) : 100;

                HistorialPrecio::create([
                    'producto_id' => $producto->id,
                    'precio_anterior' => $precioAnterior,
                    'precio_nuevo' => $producto->precio_venta,
                    'diferencia' => $diferencia,
                    'porcentaje_cambio' => $porcentaje,
                    'motivo' => 'Actualización manual',
                    'fecha_cambio' => now()
                ]);
            }

            // REDIRECCIÓN CORREGIDA
            return redirect()->route('productos.index')
                ->with('success', '✅ Producto "' . $producto->nombre . '" actualizado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al actualizar producto: ' . $e->getMessage());
            return back()->with('error', '❌ Error al actualizar producto: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Producto $producto)
    {
        try {
            // Verificar si el producto tiene ventas asociadas
            if ($producto->ventaDetalles()->exists()) {
                return back()->with('error', '❌ No se puede eliminar el producto porque tiene ventas asociadas.');
            }

            // Eliminar imagen si existe
            if ($producto->foto) {
                Storage::disk('public')->delete($producto->foto);
            }

            // Eliminar imagen de código de barras si existe
            if ($producto->codigo_barras_imagen) {
                Storage::disk('public')->delete($producto->codigo_barras_imagen);
            }

            $productoNombre = $producto->nombre;
            $producto->delete();

            return redirect()->route('productos.index')
                ->with('success', '✅ Producto "' . $productoNombre . '" eliminado exitosamente');

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al eliminar producto: ' . $e->getMessage());
        }
    }

   public function generarPDF()
{
    $productos = Producto::with(['proveedor'])->get();
    
    // SOLUCIÓN: Quitar los códigos de barras temporalmente o usar una alternativa simple
    $pdf = Pdf::loadView('productos.pdf', compact('productos'));
    
    // Opciones para mejor visualización
    $pdf->setPaper('A4', 'landscape');
    $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
    
    return $pdf->download('productos_' . date('Y-m-d') . '.pdf');
}

    public function historialPrecios(Producto $producto)
    {
        $historial = $producto->historialPrecios()->latest()->get();
        return view('productos.historial_precios', compact('producto', 'historial'));
    }

    public function actualizarPrecio(Request $request, Producto $producto)
    {
        try {
            $request->validate([
                'precio_venta' => 'required|numeric|min:0',
                'motivo' => 'required|string|max:255'
            ]);

            $precioAnterior = $producto->precio_venta;
            $precioNuevo = $request->precio_venta;

            // Si el precio no cambió, redirigir sin hacer nada
            if ($precioAnterior == $precioNuevo) {
                return redirect()->route('productos.historial-precios', $producto)
                    ->with('info', 'ℹ️ El precio no ha cambiado.');
            }

            // Actualizar precio
            $producto->update(['precio_venta' => $precioNuevo]);

            // Registrar en historial
            $diferencia = $precioNuevo - $precioAnterior;
            $porcentaje = $precioAnterior > 0 ? (($diferencia / $precioAnterior) * 100) : 100;

            HistorialPrecio::create([
                'producto_id' => $producto->id,
                'precio_anterior' => $precioAnterior,
                'precio_nuevo' => $precioNuevo,
                'diferencia' => $diferencia,
                'porcentaje_cambio' => $porcentaje,
                'motivo' => $request->motivo,
                'fecha_cambio' => now()
            ]);

            return redirect()->route('productos.historial-precios', $producto)
                ->with('success', '✅ Precio actualizado exitosamente');

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al actualizar precio: ' . $e->getMessage());
        }
    }

    public function eliminarRegistroHistorial(Producto $producto, $historialId)
    {
        try {
            $registro = HistorialPrecio::where('producto_id', $producto->id)
                ->where('id', $historialId)
                ->firstOrFail();

            $registro->delete();

            return redirect()->route('productos.historial-precios', $producto)
                ->with('success', '✅ Registro de historial eliminado exitosamente');

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al eliminar registro: ' . $e->getMessage());
        }
    }
}