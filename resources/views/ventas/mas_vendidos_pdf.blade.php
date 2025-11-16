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
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th, table td {
            border: 1px solid #444;
            padding: 6px;
            text-align: center;
        }

        table th {
            background: #eee;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #777;
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
            @foreach($productos as $index => $p)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $p->nombre }}</td>
                <td>{{ $p->total_vendido }}</td>
                <td>${{ number_format($p->total_generado, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Reporte generado automáticamente por el Sistema de Inventario — {{ date('d/m/Y H:i') }}
    </div>

</body>
</html>
