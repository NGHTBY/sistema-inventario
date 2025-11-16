@php
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\DNS1D;
@endphp

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header con Acciones -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Gestión de Productos</h1>
        <div class="flex space-x-3">
            <a href="{{ route('productos.pdf') }}" 
               class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded flex items-center">
                📊 PDF
            </a>
            <a href="{{ route('productos.create') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded flex items-center">
                ➕ Nuevo Producto
            </a>
        </div>
    </div>

    <!-- Alertas -->
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

    <!-- Tarjetas de Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $totalProductos }}</div>
            <div class="text-sm text-blue-800">Total Productos</div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border border-green-200 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $productosActivos }}</div>
            <div class="text-sm text-green-800">Productos Activos</div>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200 text-center">
            <div class="text-2xl font-bold text-yellow-600">{{ $stockBajo }}</div>
            <div class="text-sm text-yellow-800">Stock Bajo</div>
        </div>
        <div class="bg-red-50 p-4 rounded-lg border border-red-200 text-center">
            <div class="text-2xl font-bold text-red-600">{{ $sinStock }}</div>
            <div class="text-sm text-red-800">Sin Stock</div>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200 text-center">
            <div class="text-2xl font-bold text-purple-600">{{ $stockAlto ?? 0 }}</div>
            <div class="text-sm text-purple-800">Stock Alto</div>
        </div>
    </div>

    <!-- Barra de Búsqueda y Filtros -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow-md">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- Barra de Búsqueda -->
            <div class="flex-1">
                <form action="{{ route('productos.index') }}" method="GET" class="flex gap-2">
                    <div class="flex-1 relative">
                        <input type="text" 
                               name="search" 
                               id="search"
                               value="{{ request('search') }}"
                               placeholder="🔍 Buscar productos por nombre, código, categoría..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center transition duration-200">
                        Buscar
                    </button>
                    @if(request('search'))
                        <a href="{{ route('productos.index') }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center transition duration-200">
                            Limpiar
                        </a>
                    @endif
                </form>
            </div>

            <!-- Filtros Rápidos -->
            <div class="flex space-x-2">
                <a href="{{ route('productos.index') }}" 
                   class="px-4 py-2 rounded {{ !request('stock') && !request('search') ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }} transition duration-200">
                    Todos
                </a>
                <a href="?stock=bajo{{ request('search') ? '&search=' . request('search') : '' }}" 
                   class="px-4 py-2 rounded {{ request('stock') == 'bajo' ? 'bg-yellow-500 text-white' : 'bg-gray-200 text-gray-700' }} transition duration-200">
                    Stock Bajo
                </a>
                <a href="?stock=agotado{{ request('search') ? '&search=' . request('search') : '' }}" 
                   class="px-4 py-2 rounded {{ request('stock') == 'agotado' ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700' }} transition duration-200">
                    Sin Stock
                </a>
                <a href="?stock=alto{{ request('search') ? '&search=' . request('search') : '' }}" 
                   class="px-4 py-2 rounded {{ request('stock') == 'alto' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700' }} transition duration-200">
                    Stock Alto
                </a>
            </div>
        </div>

        <!-- Información de búsqueda -->
        @if(request('search'))
            <div class="mt-3 text-sm text-gray-600">
                📊 Mostrando resultados para: "<strong>{{ request('search') }}</strong>"
                @if(request('stock'))
                    con filtro: <strong>{{ ucfirst(request('stock')) }} Stock</strong>
                @endif
                <span class="ml-2">({{ $productos->total() }} resultados)</span>
            </div>
        @endif
    </div>

    <!-- Tabla de Productos -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left">Código</th>
                        <th class="py-3 px-4 text-left">Código Barras</th>
                        <th class="py-3 px-4 text-left">Nombre</th>
                        <th class="py-3 px-4 text-left">Precio Venta</th>
                        <th class="py-3 px-4 text-left">Categoría</th>
                        <th class="py-3 px-4 text-left">Stock</th>
                        <th class="py-3 px-4 text-left">Imagen</th>
                        <th class="py-3 px-4 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($productos as $producto)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <!-- Código Interno -->
                        <td class="py-3 px-4">
                            <div class="font-mono text-sm bg-gray-100 px-2 py-1 rounded border">
                                {{ $producto->codigo }}
                            </div>
                        </td>
                        
                        <!-- Código de Barras - CORREGIDO -->
                        <td class="py-3 px-4">
                            @if($producto->codigo_barras)
                                <div class="flex flex-col space-y-1 items-center">
                                    @if($producto->barcode_image_url)
                                        <!-- SOLUCIÓN: Tamaño fijo para todas las imágenes -->
                                        <img src="{{ $producto->barcode_image_url }}" 
                                             alt="Código barras" 
                                             class="h-10 w-32 object-contain border rounded mx-auto bg-white p-1">
                                    @else
                                        @php
                                            $dns1d = new DNS1D();
                                            // SOLUCIÓN: Mismo tamaño para códigos generados en tiempo real
                                            $barcodePNG = $dns1d->getBarcodePNG($producto->codigo_barras, 'C128', 2, 50);
                                        @endphp
                                        @if($barcodePNG)
                                            <img src="data:image/png;base64,{{ $barcodePNG }}" 
                                                 alt="Código barras" 
                                                 class="h-10 w-32 object-contain border rounded mx-auto bg-white p-1">
                                        @endif
                                    @endif
                                    <span class="font-mono text-xs bg-blue-50 px-2 py-1 rounded text-center">
                                        {{ $producto->codigo_barras }}
                                    </span>
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">Generando...</span>
                            @endif
                        </td>
                        
                        <!-- Nombre -->
                        <td class="py-3 px-4">
                            <div>
                                <div class="font-semibold text-gray-800">{{ $producto->nombre }}</div>
                                @if($producto->descripcion)
                                    <div class="text-sm text-gray-600 mt-1">{{ Str::limit($producto->descripcion, 50) }}</div>
                                @endif
                                @if($producto->proveedor)
                                    <div class="text-xs text-blue-600 mt-1">
                                        Prov: {{ $producto->proveedor->empresa }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        
                        <!-- Precio Venta -->
                        <td class="py-3 px-4">
                            <div class="font-semibold text-green-600">
                                ${{ number_format($producto->precio_venta, 0, ',', '.') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                Compra: ${{ number_format($producto->precio_compra, 0, ',', '.') }}
                            </div>
                        </td>
                        
                        <!-- Categoría -->
                        <td class="py-3 px-4">
                            @if($producto->categoria)
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                    {{ is_object($producto->categoria) ? $producto->categoria->nombre : $producto->categoria }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">Sin categoría</span>
                            @endif
                        </td>
                        
                        <!-- Stock -->
                        <td class="py-3 px-4">
                            <div class="flex flex-col space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold {{ $producto->stock == 0 ? 'text-red-600' : ($producto->stock_bajo ? 'text-yellow-600' : 'text-gray-800') }}">
                                        {{ number_format($producto->stock, 0, ',', '.') }}
                                    </span>
                                    @if($producto->stock == 0)
                                        <span class="ml-2 bg-red-100 text-red-800 text-xs px-2 py-1 rounded">
                                            ❌ Agotado
                                        </span>
                                    @elseif($producto->stock_bajo)
                                        <span class="ml-2 bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">
                                            ⚠ Bajo
                                        </span>
                                    @elseif($producto->stock_alto)
                                        <span class="ml-2 bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                                            ✅ Alto
                                        </span>
                                    @else
                                        <span class="ml-2 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                            ✔ Normal
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">
                                    Mín: {{ $producto->stock_minimo }} | Máx: {{ $producto->stock_maximo }}
                                </div>
                            </div>
                        </td>
                        
                        <!-- Imagen -->
                        <td class="py-3 px-4">
                            @if($producto->foto && Storage::disk('public')->exists($producto->foto))
                                <img src="{{ asset('storage/' . $producto->foto) }}" 
                                     alt="{{ $producto->nombre }}"
                                     class="w-12 h-12 rounded-lg object-cover border shadow-sm">
                            @else
                                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center border">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                        </td>
                        
                        <!-- Acciones -->
                        <td class="py-3 px-4">
                            <div class="flex flex-col space-y-2">
                                <div class="flex space-x-1">
                                    <a href="{{ route('productos.show', $producto) }}" 
                                       class="bg-blue-500 hover:bg-blue-600 text-white py-1 px-2 rounded text-xs flex items-center">
                                        👁 Ver
                                    </a>
                                    <a href="{{ route('productos.edit', $producto) }}" 
                                       class="bg-green-500 hover:bg-green-600 text-white py-1 px-2 rounded text-xs flex items-center">
                                        ✏ Editar
                                    </a>
                                </div>
                                <div class="flex space-x-1">
                                    <a href="{{ route('productos.historial-precios', $producto) }}" 
                                       class="bg-purple-500 hover:bg-purple-600 text-white py-1 px-2 rounded text-xs flex items-center">
                                        📈 Precios
                                    </a>
                                    <form action="{{ route('productos.destroy', $producto) }}" method="POST" 
                                          onsubmit="return confirm('¿Estás seguro de eliminar este producto?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="bg-red-500 hover:bg-red-600 text-white py-1 px-2 rounded text-xs flex items-center">
                                            🗑 Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 px-4 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="text-lg">
                                    @if(request('search') || request('stock'))
                                        No se encontraron productos que coincidan con tu búsqueda.
                                    @else
                                        No hay productos registrados.
                                    @endif
                                </p>
                                @if(request('search') || request('stock'))
                                    <a href="{{ route('productos.index') }}" class="text-blue-500 hover:text-blue-600 mt-2">
                                        Ver todos los productos
                                    </a>
                                @else
                                    <a href="{{ route('productos.create') }}" class="text-blue-500 hover:text-blue-600 mt-2">
                                        Crear el primer producto
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación Mejorada -->
        @if($productos->hasPages())
        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Mostrando 
                    <span class="font-semibold">{{ $productos->firstItem() }}</span>
                    a 
                    <span class="font-semibold">{{ $productos->lastItem() }}</span>
                    de 
                    <span class="font-semibold">{{ $productos->total() }}</span>
                    resultados
                </div>
                <div class="flex space-x-2">
                    <!-- Botón Anterior -->
                    @if($productos->onFirstPage())
                        <span class="px-3 py-1 bg-gray-200 text-gray-500 rounded cursor-not-allowed">
                            ← Anterior
                        </span>
                    @else
                        <a href="{{ $productos->previousPageUrl() }}{{ request('search') ? '&search=' . request('search') : '' }}{{ request('stock') ? '&stock=' . request('stock') : '' }}" 
                           class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-200">
                            ← Anterior
                        </a>
                    @endif

                    <!-- Números de Página -->
                    @foreach ($productos->getUrlRange(1, $productos->lastPage()) as $page => $url)
                        @if ($page == $productos->currentPage())
                            <span class="px-3 py-1 bg-blue-600 text-white rounded">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}{{ request('search') ? '&search=' . request('search') : '' }}{{ request('stock') ? '&stock=' . request('stock') : '' }}" 
                               class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition duration-200">{{ $page }}</a>
                        @endif
                    @endforeach

                    <!-- Botón Siguiente -->
                    @if($productos->hasMorePages())
                        <a href="{{ $productos->nextPageUrl() }}{{ request('search') ? '&search=' . request('search') : '' }}{{ request('stock') ? '&stock=' . request('stock') : '' }}" 
                           class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-200">
                            Siguiente →
                        </a>
                    @else
                        <span class="px-3 py-1 bg-gray-200 text-gray-500 rounded cursor-not-allowed">
                            Siguiente →
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    .bg-blue-50 { background-color: #eff6ff; }
    .bg-green-50 { background-color: #f0fdf4; }
    .bg-yellow-50 { background-color: #fefce8; }
    .bg-red-50 { background-color: #fef2f2; }
    .bg-purple-50 { background-color: #faf5ff; }
</style>

<script>
    // Auto-focus en el campo de búsqueda
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search');
        if (searchInput) {
            searchInput.focus();
            // Colocar el cursor al final del texto
            searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
        }
    });
</script>
@endsection