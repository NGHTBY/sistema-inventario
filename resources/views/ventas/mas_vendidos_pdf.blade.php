<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte - Productos más vendidos</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #1f2937;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }

        table td {
            border: 1px solid #d1d5db;
            padding: 8px;
        }

        .producto-nombre {
            font-weight: bold;
            color: #1f2937;
        }

        .producto-codigo {
            font-size: 11px;
            color: #6b7280;
        }

        .total-generado {
            font-weight: bold;
            color: #059669;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #777;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-style: italic;
        }
    </style>
</head>

<body>

    <h2>📈 Reporte de Productos Más Vendidos</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Total Vendido (unidades)</th>
                <th>Total Generado ($)</th>
            </tr>
        </thead>

        <tbody>
            @forelse($productos as $index => $p)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <div class="producto-nombre">{{ $p->producto->nombre ?? 'Producto no encontrado' }}</div>
                    <div class="producto-codigo">Código: {{ $p->producto->codigo_barras ?? 'N/A' }}</div>
                </td>
                <td>{{ $p->total_vendido }} unidades</td>
                <td class="total-generado">${{ number_format($p->total_generado, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="no-data">No hay ventas registradas</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Reporte generado automáticamente por el Sistema de Inventario — {{ date('d/m/Y H:i') }}
    </div>

</body>
</html>