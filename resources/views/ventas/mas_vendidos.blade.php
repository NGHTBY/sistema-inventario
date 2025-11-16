@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto bg-white p-6 rounded-xl shadow">
    <h2 class="text-2xl font-bold text-center mb-6">
        📈 Productos Más Vendidos
    </h2>

    <div class="flex justify-between mb-4">
        <a href="{{ route('ventas.index') }}"
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
           ⬅ Volver a ventas
        </a>

        <a href="{{ route('ventas.mas-vendidos-pdf') }}"
           class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
           📄 Descargar PDF
        </a>
    </div>

    @if($productosMasVendidos->count() > 0)
        <table class="min-w-full bg-white border border-gray-300 rounded-lg overflow-hidden">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="px-4 py-2 border">#</th>
                    <th class="px-4 py-2 border">Producto</th>
                    <th class="px-4 py-2 border">Total Vendido</th>
                    <th class="px-4 py-2 border">Total Generado ($)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productosMasVendidos as $index => $item)
                <tr class="{{ $index % 2 === 0 ? 'bg-gray-50' : 'bg-white' }} hover:bg-blue-50">
                    <td class="px-4 py-2 border text-center">{{ $index + 1 }}</td>
                    <td class="px-4 py-2 border">
                        @if($item->producto)
                            {{ $item->producto->nombre }}
                            @if($item->producto->codigo)
                                <br><small class="text-gray-500">Código: {{ $item->producto->codigo }}</small>
                            @endif
                        @else
                            <span class="text-red-500">Producto eliminado</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 border text-center font-semibold">
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                            {{ number_format($item->total_vendido) }} unidades
                        </span>
                    </td>
                    <td class="px-4 py-2 border text-center font-bold text-green-600">
                        ${{ number_format($item->total_ingresos, 0) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="text-center py-8 bg-gray-50 rounded-lg border border-gray-200">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">No hay datos de ventas</h3>
            <p class="text-gray-500">No se han registrado ventas aún.</p>
            <a href="{{ route('ventas.create') }}" class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                ➕ Registrar primera venta
            </a>
        </div>
    @endif
</div>
@endsection