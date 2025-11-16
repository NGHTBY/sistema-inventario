@php
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\DNS1D;
@endphp

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6 max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Detalles del Producto</h2>
            <div class="flex space-x-2">
                <a href="{{ route('productos.edit', $producto) }}" 
                   class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                    ✏️ Editar
                </a>
                <a href="{{ route('productos.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    ← Volver
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Información del Producto -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Códigos -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Código Interno</label>
                        <p class="mt-1 font-mono text-lg bg-gray-50 p-2 rounded border">{{ $producto->codigo }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Código de Barras</label>
                        <p class="mt-1 font-mono text-lg bg-gray-50 p-2 rounded border">{{ $producto->codigo_barras ?? 'No generado' }}</p>
                    </div>
                </div>

                <!-- Código de Barras Visual -->
                @if($producto->codigo_barras)
                <div class="bg-white p-4 rounded-lg border">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Código de Barras</label>
                    <div class="flex flex-col items-center space-y-2">
                        @if($producto->barcode_image_url)
                            <img src="{{ $producto->barcode_image_url }}" alt="Código de barras" class="h-20 border rounded">
                        @else
                            @php
                                $dns1d = new DNS1D();
                                $barcodePNG = $dns1d->getBarcodePNG($producto->codigo_barras, 'C128', 2, 60);
                            @endphp
                            @if($barcodePNG)
                                <img src="data:image/png;base64,{{ $barcodePNG }}" alt="Código de barras" class="h-20 border rounded">
                            @endif
                        @endif
                        <p class="text-sm font-mono text-gray-600 bg-blue-50 px-3 py-1 rounded">{{ $producto->codigo_barras }}</p>
                    </div>
                </div>
                @endif

                <!-- Información Básica -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre del Producto</label>
                    <p class="mt-1 text-xl font-semibold text-gray-900">{{ $producto->nombre }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Categoría</label>
                        <p class="mt-1">
                            <span class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded font-medium">
                                {{ $producto->categoria }}
                            </span>
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estado</label>
                        <p class="mt-1">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $producto->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $producto->activo ? '✅ Activo' : '❌ Inactivo' }}
                            </span>
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Descripción</label>
                    <div class="mt-1 p-3 bg-gray-50 rounded border">
                        <p class="text-gray-600">{{ $producto->descripcion ?: 'Sin descripción' }}</p>
                    </div>
                </div>

                <!-- Precios -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded border">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Precio de Compra</label>
                        <p class="text-lg font-semibold text-gray-800">${{ number_format($producto->precio_compra, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded border border-green-200">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Precio de Venta</label>
                        <p class="text-xl font-bold text-green-600">${{ number_format($producto->precio_venta, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Stock -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center p-4 rounded border {{ $producto->stock == 0 ? 'bg-red-50 border-red-200' : ($producto->stock_bajo ? 'bg-yellow-50 border-yellow-200' : 'bg-gray-50') }}">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock Actual</label>
                        <p class="text-2xl font-bold {{ $producto->stock == 0 ? 'text-red-600' : ($producto->stock_bajo ? 'text-yellow-600' : 'text-gray-800') }}">
                            {{ number_format($producto->stock, 0, ',', '.') }}
                        </p>
                        @if($producto->stock == 0)
                            <span class="text-xs font-semibold text-red-600 mt-1">¡AGOTADO!</span>
                        @elseif($producto->stock_bajo)
                            <span class="text-xs font-semibold text-yellow-600 mt-1">¡STOCK BAJO!</span>
                        @endif
                    </div>
                    <div class="text-center p-4 rounded border bg-blue-50 border-blue-200">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock Mínimo</label>
                        <p class="text-lg font-semibold text-blue-600">{{ number_format($producto->stock_minimo, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-center p-4 rounded border bg-blue-50 border-blue-200">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock Máximo</label>
                        <p class="text-lg font-semibold text-blue-600">{{ number_format($producto->stock_maximo, 0, ',', '.') }}</p>
                    </div>
                </div>

                @if($producto->proveedor)
                <div class="bg-blue-50 p-4 rounded border border-blue-200">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Proveedor</label>
                    <div class="space-y-1">
                        <p class="font-semibold text-blue-800">{{ $producto->proveedor->empresa }}</p>
                        <p class="text-sm text-gray-600">Contacto: {{ $producto->proveedor->contacto }}</p>
                        @if($producto->proveedor->telefono)
                            <p class="text-sm text-gray-600">Tel: {{ $producto->proveedor->telefono }}</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Historial de Precios y Imagen -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Imagen del Producto -->
                <div class="bg-white border rounded-lg shadow-sm">
                    <div class="bg-gray-50 px-4 py-3 border-b">
                        <h3 class="text-lg font-semibold text-gray-800">🖼️ Imagen del Producto</h3>
                    </div>
                    <div class="p-6 flex justify-center">
                        @if($producto->foto && Storage::disk('public')->exists($producto->foto))
                            <img src="{{ asset('storage/' . $producto->foto) }}" 
                                 alt="{{ $producto->nombre }}"
                                 class="w-64 h-64 rounded-lg object-cover shadow-md border">
                        @else
                            <div class="w-64 h-64 bg-gray-200 rounded-lg flex flex-col items-center justify-center shadow-md border">
                                <svg class="w-16 h-16 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-gray-500 text-sm">Sin imagen</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Historial de Precios -->
                <div class="bg-white border rounded-lg shadow-sm">
                    <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">📊 Historial de Precios</h3>
                        <a href="{{ route('productos.historial-precios', $producto) }}" 
                           class="bg-purple-500 hover:bg-purple-600 text-white text-sm py-1 px-3 rounded">
                            Ver Todo
                        </a>
                    </div>
                    
                    <div class="p-4">
                        @if($producto->historialPrecios->count() > 0)
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                @foreach($producto->historialPrecios->take(5) as $historial)
                                <div class="border rounded-lg p-3 hover:bg-gray-50 transition duration-150">
                                    <div class="flex justify-between items-start">
                                        <div class="flex items-center space-x-2">
                                            @if($historial->diferencia > 0)
                                                <span class="text-lg text-red-500">📈</span>
                                            @elseif($historial->diferencia < 0)
                                                <span class="text-lg text-green-500">📉</span>
                                            @else
                                                <span class="text-lg text-gray-500">➡️</span>
                                            @endif
                                            <div>
                                                <p class="font-semibold {{ $historial->diferencia > 0 ? 'text-red-600' : ($historial->diferencia < 0 ? 'text-green-600' : 'text-gray-600') }}">
                                                    {{ $historial->diferencia > 0 ? 'Aumentó' : ($historial->diferencia < 0 ? 'Disminuyó' : 'Sin cambio') }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $historial->fecha_cambio->format('d/m/Y H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm line-through text-gray-400">
                                                ${{ number_format($historial->precio_anterior, 0, ',', '.') }}
                                            </p>
                                            <p class="font-semibold {{ $historial->diferencia > 0 ? 'text-red-600' : ($historial->diferencia < 0 ? 'text-green-600' : 'text-gray-600') }}">
                                                ${{ number_format($historial->precio_nuevo, 0, ',', '.') }}
                                            </p>
                                            <p class="text-xs {{ $historial->diferencia > 0 ? 'text-red-600' : ($historial->diferencia < 0 ? 'text-green-600' : 'text-gray-600') }}">
                                                {{ $historial->diferencia > 0 ? '+' : '' }}${{ number_format(abs($historial->diferencia), 0, ',', '.') }}
                                                ({{ $historial->diferencia > 0 ? '+' : '' }}{{ number_format($historial->porcentaje_cambio, 1) }}%)
                                            </p>
                                        </div>
                                    </div>
                                    @if($historial->motivo)
                                    <p class="text-xs text-gray-500 mt-2">
                                        <strong>Motivo:</strong> {{ $historial->motivo }}
                                    </p>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            
                            @if($producto->historialPrecios->count() > 5)
                                <div class="mt-3 text-center">
                                    <a href="{{ route('productos.historial-precios', $producto) }}" 
                                       class="text-blue-500 hover:text-blue-600 text-sm">
                                        Ver {{ $producto->historialPrecios->count() - 5 }} registros más...
                                    </a>
                                </div>
                            @endif
                            
                            <!-- Estadísticas -->
                            @php
                                $estadisticas = $producto->estadisticasPrecios(30);
                            @endphp
                            <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                                <div class="bg-blue-50 p-3 rounded border">
                                    <p class="text-xl font-bold text-blue-600">{{ $estadisticas['total_cambios'] }}</p>
                                    <p class="text-xs text-blue-800">Cambios (30 días)</p>
                                </div>
                                <div class="bg-red-50 p-3 rounded border">
                                    <p class="text-xl font-bold text-red-600">{{ $estadisticas['aumentos'] }}</p>
                                    <p class="text-xs text-red-800">Aumentos</p>
                                </div>
                                <div class="bg-green-50 p-3 rounded border">
                                    <p class="text-xl font-bold text-green-600">{{ $estadisticas['disminuciones'] }}</p>
                                    <p class="text-xs text-green-800">Disminuciones</p>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="text-4xl mb-2">📊</div>
                                <p class="text-gray-500">No hay historial de precios registrado</p>
                                <p class="text-sm text-gray-400 mt-1">Los cambios de precio se registrarán automáticamente</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="bg-white border rounded-lg shadow-sm">
                    <div class="bg-gray-50 px-4 py-3 border-b">
                        <h3 class="text-lg font-semibold text-gray-800">⚡ Acciones Rápidas</h3>
                    </div>
                    <div class="p-4 space-y-2">
                        <a href="{{ route('productos.edit', $producto) }}" 
                           class="w-full bg-green-500 hover:bg-green-600 text-white text-center py-2 px-4 rounded block transition duration-200">
                            ✏️ Editar Producto
                        </a>
                        
                        <a href="{{ route('productos.historial-precios', $producto) }}" 
                           class="w-full bg-purple-500 hover:bg-purple-600 text-white text-center py-2 px-4 rounded block transition duration-200">
                            📈 Gestionar Precios
                        </a>
                        
                        <form action="{{ route('productos.destroy', $producto) }}" method="POST"
                              onsubmit="return confirm('¿Estás seguro de eliminar este producto? Esta acción no se puede deshacer.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded transition duration-200">
                                🗑️ Eliminar Producto
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection