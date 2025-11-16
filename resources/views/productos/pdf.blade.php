<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Productos</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 12px; 
            margin: 20px;
        }
        h2 { 
            text-align: center; 
            color: #1e40af; 
            margin-bottom: 20px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        th, td { 
            border: 1px solid #999; 
            padding: 8px; 
            text-align: left; 
        }
        th { 
            background-color: #dbeafe; 
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .barcode-text { 
            font-family: 'Courier New', monospace; 
            font-size: 10px; 
            letter-spacing: 1px;
        }
        .footer {
            text-align: center; 
            margin-top: 30px; 
            font-size: 11px; 
            color: #555;
        }
        .stock-bajo { color: #dc2626; font-weight: bold; }
        .stock-normal { color: #16a34a; }
        .stock-alto { color: #ca8a04; }
    </style>
</head>
<body>
    <h2>📦 Reporte de Productos - Sistema de Inventario</h2>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Precio Compra</th>
                <th>Precio Venta</th>
                <th>Stock</th>
                <th>Stock Mín</th>
                <th>Stock Máx</th>
                <th>Proveedor</th>
                <th>Código Barras</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($productos as $producto)
                <tr>
                    <td class="text-center"><strong>{{ $producto->codigo }}</strong></td>
                    <td>{{ $producto->nombre }}</td>
                    <td>{{ $producto->categoria }}</td>
                    <td class="text-center">${{ number_format($producto->precio_compra, 2) }}</td>
                    <td class="text-center">${{ number_format($producto->precio_venta, 2) }}</td>
                    <td class="text-center 
                        @if($producto->stock <= $producto->stock_minimo) stock-bajo
                        @elseif($producto->stock >= $producto->stock_maximo) stock-alto
                        @else stock-normal @endif">
                        {{ $producto->stock }}
                    </td>
                    <td class="text-center">{{ $producto->stock_minimo }}</td>
                    <td class="text-center">{{ $producto->stock_maximo }}</td>
                    <td>
                        @if($producto->proveedor)
                            {{ $producto->proveedor->empresa }}
                        @else
                            <span style="color: #999;">Sin proveedor</span>
                        @endif
                    </td>
                    <td class="text-center barcode-text">
                        {{ $producto->codigo_barras ?: 'N/A' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Resumen -->
    <div style="margin-top: 20px; padding: 15px; background-color: #f8fafc; border-radius: 5px;">
        <h3 style="margin-bottom: 10px; color: #1e40af;">Resumen del Inventario</h3>
        <p><strong>Total de productos:</strong> {{ $productos->count() }}</p>
        <p><strong>Productos con stock bajo:</strong> {{ $productos->where('stock', '<=', \DB::raw('stock_minimo'))->count() }}</p>
        <p><strong>Productos sin stock:</strong> {{ $productos->where('stock', 0)->count() }}</p>
    </div>

    <div class="footer">
        Generado automáticamente el {{ date('d/m/Y H:i') }} por el Sistema de Inventario | Grupo 5-6
    </div>
</body>
</html>