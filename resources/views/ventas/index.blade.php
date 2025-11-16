@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Listado de Ventas</h2>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <a href="{{ route('ventas.create') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                + Nueva Venta
            </a>
            <div class="space-x-2">
                <a href="{{ route('ventas.reporte.mas_vendidos') }}" 
                   class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                    📈 Productos Más Vendidos
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left">#</th>
                        <th class="py-3 px-4 text-left">Factura</th>
                        <th class="py-3 px-4 text-left">Total</th>
                        <th class="py-3 px-4 text-left">Fecha</th>
                        <th class="py-3 px-4 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $venta)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="py-3 px-4">{{ $venta->id }}</td>
                        <td class="py-3 px-4 font-semibold">{{ $venta->factura }}</td>
                        <td class="py-3 px-4">${{ number_format($venta->total, 2) }}</td>
                        <td class="py-3 px-4">{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 px-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('ventas.show', $venta) }}" 
                                   class="bg-blue-500 hover:bg-blue-600 text-white py-1 px-3 rounded text-sm">
                                    Ver
                                </a>
                                <a href="{{ route('ventas.pdf', $venta) }}" 
                                   class="bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded text-sm">
                                    PDF
                                </a>
                                <form action="{{ route('ventas.destroy', $venta) }}" method="POST" 
                                      onsubmit="return confirm('¿Estás seguro de eliminar esta venta?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="bg-gray-500 hover:bg-gray-600 text-white py-1 px-3 rounded text-sm">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-4 px-4 text-center text-gray-500">
                            No hay ventas registradas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection