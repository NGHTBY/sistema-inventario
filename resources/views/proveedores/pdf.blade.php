<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Proveedores</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #2c5282;
            margin: 0;
            font-size: 24px;
        }
        .header .subtitle {
            color: #666;
            font-size: 14px;
        }
        .info-section {
            margin-bottom: 20px;
            background: #f8fafc;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #2c5282;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th {
            background-color: #2c5282;
            color: white;
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .table td {
            padding: 8px 10px;
            border: 1px solid #ddd;
        }
        .table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .status-active {
            background-color: #c6f6d5;
            color: #22543d;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
        }
        .status-inactive {
            background-color: #fed7d7;
            color: #742a2a;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
        }
        .badge {
            background-color: #bee3f8;
            color: #1a365d;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .summary-item {
            background: #f7fafc;
            padding: 10px 15px;
            border-radius: 5px;
            border-left: 3px solid #2c5282;
            flex: 1;
            margin: 0 5px;
            min-width: 120px;
        }
        .summary-item h3 {
            margin: 0;
            font-size: 12px;
            color: #4a5568;
        }
        .summary-item .number {
            font-size: 18px;
            font-weight: bold;
            color: #2c5282;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Proveedores</h1>
        <div class="subtitle">
            Sistema de Inventario - Generado el: {{ date('d/m/Y H:i:s') }}
        </div>
    </div>

    <!-- Resumen Estadístico -->
    <div class="summary">
        <div class="summary-item">
            <h3>Total Proveedores</h3>
            <div class="number">{{ $proveedores->count() }}</div>
        </div>
        <div class="summary-item">
            <h3>Proveedores Activos</h3>
            <div class="number">{{ $proveedores->where('activo', true)->count() }}</div>
        </div>
        <div class="summary-item">
            <h3>Total Productos</h3>
            <div class="number">
                @php
                    $totalProductos = $proveedores->sum(function($proveedor) {
                        return $proveedor->productos->count();
                    });
                @endphp
                {{ $totalProductos }}
            </div>
        </div>
        <div class="summary-item">
            <h3>Con Productos</h3>
            <div class="number">
                @php
                    $conProductos = $proveedores->filter(function($proveedor) {
                        return $proveedor->productos->count() > 0;
                    })->count();
                @endphp
                {{ $conProductos }}
            </div>
        </div>
    </div>

    <!-- Tabla de Proveedores -->
    <table class="table">
        <thead>
            <tr>
                <th>Empresa</th>
                <th>Contacto</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>NIT</th>
                <th>Productos</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proveedores as $proveedor)
            <tr>
                <td style="font-weight: bold;">{{ $proveedor->empresa }}</td>
                <td>{{ $proveedor->contacto }}</td>
                <td>{{ $proveedor->telefono ?: 'N/A' }}</td>
                <td>{{ $proveedor->email ?: 'N/A' }}</td>
                <td>{{ $proveedor->nit ?: 'N/A' }}</td>
                <td>
                    <span class="badge">
                        {{ $proveedor->productos->count() }} productos
                    </span>
                </td>
                <td>
                    @if($proveedor->activo)
                        <span class="status-active">ACTIVO</span>
                    @else
                        <span class="status-inactive">INACTIVO</span>
                    @endif
                </td>
            </tr>
            @if($proveedor->direccion)
            <tr>
                <td colspan="7" style="background-color: #f0f4f8; font-size: 10px; padding: 5px 10px;">
                    <strong>Dirección:</strong> {{ $proveedor->direccion }}
                </td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Reporte generado automáticamente por el Sistema de Inventario</p>
        <p>Página 1 de 1</p>
    </div>
</body>
</html>