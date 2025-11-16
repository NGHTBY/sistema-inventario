@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-8 bg-white shadow-lg rounded-xl p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">📋 Detalle de Venta</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Información de la Venta</h3>
            <p class="text-gray-600"><strong>Factura:</strong> 
                <span class="font-mono text-blue-600">{{ $venta->factura }}</span>
            </p>
            <p class="text-gray-600"><strong>Fecha:</strong> 
                {{ $venta->created_at->format('d/m/Y H:i') }}
            </p>
        </div>

        <div class="bg-green-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Total de la Venta</h3>
            <p class="text-2xl text-green-700 font-bold">
                ${{ number_format($venta->total, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <h3 class="text-xl font-semibold text-gray-800 mb-4">Productos Vendidos</h3>
    
    <table class="min-w-full border border-gray-300 rounded-lg">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="px-4 py-2 text-left">Producto</th>
                <th class="px-4 py-2 text-center">Cantidad</th>
                <th class="px-4 py-2 text-center">Precio Unitario</th>
                <th class="px-4 py-2 text-center">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($venta->detalles as $detalle)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2">
                        {{ $detalle->producto->nombre ?? 'Producto eliminado' }}
                    </td>
                    <td class="px-4 py-2 text-center">
                        {{ number_format($detalle->cantidad, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-2 text-center">
                        ${{ number_format($detalle->precio_unitario, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-2 text-center">
                        ${{ number_format($detalle->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-6 text-center space-x-4">
        <a href="{{ route('ventas.pdf', $venta) }}"
           class="bg-red-600 text-white px-6 py-2 rounded-lg shadow hover:bg-red-700 transition">
            📄 Generar PDF
        </a>
        <a href="{{ route('ventas.index') }}"
           class="bg-gray-600 text-white px-6 py-2 rounded-lg shadow hover:bg-gray-700 transition">
            ↩ Volver a Ventas
        </a>
    </div>
</div>
@endsection