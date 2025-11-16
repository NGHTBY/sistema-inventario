@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Detalles del Proveedor</h1>
            <a href="{{ route('proveedores.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                ← Volver a la lista
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Información del Proveedor -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Información General</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Empresa:</label>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $proveedor->empresa }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Contacto:</label>
                        <p class="mt-1 text-lg text-gray-900">{{ $proveedor->contacto }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Teléfono:</label>
                        <p class="mt-1 text-gray-900">{{ $proveedor->telefono ?: 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Email:</label>
                        <p class="mt-1 text-gray-900">{{ $proveedor->email ?: 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">NIT:</label>
                        <p class="mt-1 text-gray-900">{{ $proveedor->nit ?: 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Dirección:</label>
                        <p class="mt-1 text-gray-900">{{ $proveedor->direccion ?: 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Productos que Suministra -->
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">
                    Productos que Suministra 
                    <span class="bg-blue-100 text-blue-800 text-sm font-medium px-2 py-1 rounded-full">
                        {{ $proveedor->productos->count() }} productos
                    </span>
                </h2>

                @if($proveedor->productos->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Venta</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado Stock</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($proveedor->productos as $producto)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="text-sm font-mono text-gray-600">{{ $producto->codigo }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $producto->nombre }}</div>
                                        @if($producto->descripcion)
                                            <div class="text-xs text-gray-500">{{ Str::limit($producto->descripcion, 50) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ $producto->categoria }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm text-gray-900">{{ $producto->stock }}</div>
                                        <div class="text-xs text-gray-500">
                                            Mín: {{ $producto->stock_minimo }} | Máx: {{ $producto->stock_maximo }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-sm font-semibold text-green-600">
                                            ${{ number_format($producto->precio_venta, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($producto->stock <= $producto->stock_minimo)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Stock Bajo
                                            </span>
                                        @elseif($producto->stock >= $producto->stock_maximo)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Stock Alto
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Normal
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 mb-2">
                            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m8-8V4a1 1 0 00-1-1h-2a1 1 0 00-1 1v1M9 7h6"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500 text-lg">Este proveedor no tiene productos asociados.</p>
                        <p class="text-gray-400 text-sm mt-1">Los productos que se asignen a este proveedor aparecerán aquí.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="flex justify-end space-x-3 mt-6">
            <a href="{{ route('proveedores.edit', $proveedor->id) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                Editar Proveedor
            </a>
            <form action="{{ route('proveedores.destroy', $proveedor->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition"
                        onclick="return confirm('¿Estás seguro de eliminar al proveedor {{ $proveedor->empresa }}?')">
                    Eliminar Proveedor
                </button>
            </form>
        </div>
    </div>
</div>
@endsection