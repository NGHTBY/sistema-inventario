<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura {{ $venta->factura }}</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 12px; 
            color: #333; 
            margin: 20px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .info { 
            margin-bottom: 20px; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px; 
        }
        th, td { 
            border: 1px solid #aaa; 
            padding: 8px; 
            text-align: left; 
        }
        th { 
            background-color: #f0f0f0; 
        }
        .total { 
            text-align: right; 
            margin-top: 20px; 
            font-weight: bold; 
            font-size: 14px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SISTEMA DE INVENTARIO</h1>
        <h2>FACTURA DE VENTA</h2>
    </div>

    <div class="info">
        <p><strong>Factura:</strong> {{ $venta->factura }}</p>
        <p><strong>Fecha:</strong> {{ $venta->fecha->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @if($venta->detalles && $venta->detalles->count() > 0)
                @foreach ($venta->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->producto->nombre ?? 'Producto no disponible' }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>${{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
                        <td>${{ number_format($detalle->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4" style="text-align: center;">No hay items en esta venta</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="total">
        TOTAL: ${{ number_format($venta->total, 0, ',', '.') }}
    </div>

    <div class="footer">
        <p>Gracias por su compra</p>
        <p>Sistema de Inventario - {{ date('Y') }}</p>
    </div>
</body>
</html>