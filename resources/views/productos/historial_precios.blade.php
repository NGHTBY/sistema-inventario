@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold">📈 Historial de Precios</h1>
                    <p class="text-purple-100 mt-1">Producto: {{ $producto->nombre }}</p>
                    <p class="text-purple-100">Código: {{ $producto->codigo }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('productos.show', $producto) }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        ↩ Volver al Producto
                    </a>
                    <a href="{{ route('productos.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        🏠 Inicio
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Alertas Mejoradas -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('info'))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    {{ session('info') }}
                </div>
            @endif

            <!-- Información Actual del Producto -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center">
                        <div class="bg-white rounded-lg p-4 border border-green-200">
                            <label class="block text-sm font-medium text-green-600 mb-2">Precio Actual</label>
                            <p class="text-2xl font-bold text-green-700">
                                ${{ number_format($producto->precio_venta, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <div class="bg-white rounded-lg p-4 border border-blue-200">
                            <label class="block text-sm font-medium text-blue-600 mb-2">Stock Actual</label>
                            <p class="text-2xl font-bold text-blue-700">
                                {{ number_format($producto->stock, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <div class="bg-white rounded-lg p-4 border border-purple-200">
                            <label class="block text-sm font-medium text-purple-600 mb-2">Total Cambios</label>
                            <p class="text-2xl font-bold text-purple-700">
                                {{ $historial->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario para Actualizar Precio -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">🔄 Actualizar Precio</h2>
                
                <form action="{{ route('productos.actualizar-precio', $producto) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="precio_venta" class="block text-sm font-medium text-gray-700 mb-2">
                                Nuevo Precio de Venta
                            </label>
                            <input type="number" name="precio_venta" id="precio_venta" 
                                   value="{{ old('precio_venta', $producto->precio_venta) }}"
                                   step="0.01" min="0"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   required>
                        </div>
                        
                        <div>
                            <label for="motivo" class="block text-sm font-medium text-gray-700 mb-2">
                                Motivo del Cambio
                            </label>
                            <input type="text" name="motivo" id="motivo" 
                                   value="{{ old('motivo') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Ej: Ajuste por inflación" required>
                        </div>
                        
                        <div class="flex items-end">
                            <button type="submit" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200">
                                💾 Actualizar Precio
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabla de Historial -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <h2 class="text-xl font-bold text-gray-800 p-6 border-b border-gray-200">
                    📊 Historial de Cambios de Precio
                </h2>
                
                @if($historial->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Precio Anterior</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Precio Nuevo</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Diferencia</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase">% Cambio</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Motivo</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($historial as $cambio)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $cambio->fecha_cambio->format('d/m/Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $cambio->fecha_cambio->format('H:i:s') }}
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-600">
                                            ${{ number_format($cambio->precio_anterior, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-green-600">
                                            ${{ number_format($cambio->precio_nuevo, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold {{ $cambio->diferencia >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $cambio->diferencia >= 0 ? '+' : '' }}${{ number_format($cambio->diferencia, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold {{ $cambio->porcentaje_cambio >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $cambio->porcentaje_cambio >= 0 ? '+' : '' }}{{ number_format($cambio->porcentaje_cambio, 2) }}%
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-700">
                                            {{ $cambio->motivo ?? 'Sin motivo especificado' }}
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <form action="{{ route('productos.eliminar-historial', [$producto, $cambio->id]) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('¿Estás seguro de eliminar este registro del historial?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold transition duration-200">
                                                🗑 Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="flex flex-col items-center">
                            <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <p class="text-lg text-gray-600 mb-2">No hay historial de precios</p>
                            <p class="text-gray-500">Los cambios de precio aparecerán aquí</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Estadísticas del Historial -->
            @if($historial->count() > 0)
            <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 rounded-lg p-4 text-center border border-blue-200">
                    <div class="text-2xl font-bold text-blue-600">{{ $historial->count() }}</div>
                    <div class="text-sm text-blue-800">Total Cambios</div>
                </div>
                
                <div class="bg-green-50 rounded-lg p-4 text-center border border-green-200">
                    <div class="text-2xl font-bold text-green-600">
                        {{ $historial->where('diferencia', '>', 0)->count() }}
                    </div>
                    <div class="text-sm text-green-800">Aumentos</div>
                </div>
                
                <div class="bg-red-50 rounded-lg p-4 text-center border border-red-200">
                    <div class="text-2xl font-bold text-red-600">
                        {{ $historial->where('diferencia', '<', 0)->count() }}
                    </div>
                    <div class="text-sm text-red-800">Disminuciones</div>
                </div>
                
                <div class="bg-purple-50 rounded-lg p-4 text-center border border-purple-200">
                    <div class="text-2xl font-bold text-purple-600">
                        {{ number_format($historial->avg('porcentaje_cambio'), 2) }}%
                    </div>
                    <div class="text-sm text-purple-800">Cambio Promedio</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection