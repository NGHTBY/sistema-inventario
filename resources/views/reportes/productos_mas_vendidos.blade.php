@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Productos Más Vendidos</h2>

        <div class="flex justify-between items-center mb-6">
            <a href="{{ route('ventas.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                ← Volver a Ventas
            </a>
            <a href="{{ route('ventas.reporte.mas_vendidos.pdf') }}" 
               class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                📊 Generar PDF
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left">Producto</th>
                        <th class="py-3 px-4 text-left">Código</th>
                        <th class="py-3 px-4 text-left">Categoría</th>
                        <th class="py-3 px-4 text-left">Total Vendido</th>
                        <th class="py-3 px-4 text-left">Stock Actual</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productosMasVendidos as $item)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="py-3 px-4">
                            <div class="flex items-center">
                                @if($item->producto->foto)
                                    <img src="{{ asset('storage/' . $item->producto->foto) }}" 
                                         alt="{{ $item->producto->nombre }}" 
                                         class="w-10 h-10 rounded-full mr-3 object-cover">
                                @endif
                                <span>{{ $item->producto->nombre }}</span>
                            </div>
                        </td>
                        <td class="py-3 px-4 font-mono">{{ $item->producto->codigo }}</td>
                        <td class="py-3 px-4">{{ $item->producto->categoria }}</td>
                        <td class="py-3 px-4 font-semibold text-green-600">{{ $item->total_vendido }} unidades</td>
                        <td class="py-3 px-4">
                            <span class="{{ $item->producto->stock < 10 ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                {{ $item->producto->stock }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-4 px-4 text-center text-gray-500">
                            No hay datos de ventas disponibles
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection