@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-700">Listado de Proveedores</h2>
        <div class="flex space-x-3">
            <a href="{{ route('proveedores.pdf') }}" 
               class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center transition">
                📊 Generar PDF
            </a>
            <a href="{{ route('proveedores.create') }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                ➕ Nuevo Proveedor
            </a>
        </div>
    </div>

    <!-- Mensajes de éxito y error -->
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

    <!-- Estadísticas Rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $proveedores->count() }}</div>
            <div class="text-sm text-blue-800">Total Proveedores</div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border border-green-200 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $proveedores->where('activo', true)->count() }}</div>
            <div class="text-sm text-green-800">Proveedores Activos</div>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200 text-center">
            @php
                $totalProductos = $proveedores->sum(function($proveedor) {
                    return $proveedor->productos->count();
                });
            @endphp
            <div class="text-2xl font-bold text-purple-600">{{ $totalProductos }}</div>
            <div class="text-sm text-purple-800">Total Productos</div>
        </div>
        <div class="bg-orange-50 p-4 rounded-lg border border-orange-200 text-center">
            @php
                $proveedoresConProductos = $proveedores->filter(function($proveedor) {
                    return $proveedor->productos->count() > 0;
                })->count();
            @endphp
            <div class="text-2xl font-bold text-orange-600">{{ $proveedoresConProductos }}</div>
            <div class="text-sm text-orange-800">Con Productos</div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">Empresa</th>
                        <th class="px-4 py-3 text-left">Contacto</th>
                        <th class="px-4 py-3 text-left">Teléfono</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Productos</th>
                        <th class="px-4 py-3 text-left">Estado</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proveedores as $proveedor)
                        <tr class="border-b hover:bg-gray-50 transition duration-150">
                            <td class="px-4 py-3 font-semibold">
                                <div class="flex items-center">
                                    {{ $proveedor->empresa }}
                                    @if($proveedor->nit)
                                        <span class="ml-2 bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">
                                            NIT: {{ $proveedor->nit }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">{{ $proveedor->contacto }}</td>
                            <td class="px-4 py-3">
                                @if($proveedor->telefono)
                                    <a href="tel:{{ $proveedor->telefono }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $proveedor->telefono }}
                                    </a>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($proveedor->email)
                                    <a href="mailto:{{ $proveedor->email }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $proveedor->email }}
                                    </a>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($proveedor->productos->count() > 0)
                                    <span class="bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full font-semibold">
                                        {{ $proveedor->productos->count() }} productos
                                    </span>
                                @else
                                    <span class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full">
                                        Sin productos
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($proveedor->activo)
                                    <span class="bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full font-semibold">
                                        ✅ Activo
                                    </span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs px-3 py-1 rounded-full font-semibold">
                                        ❌ Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center space-x-2">
                                    <!-- Enlace para Ver -->
                                    <a href="{{ route('proveedores.show', $proveedor->id) }}" 
                                       class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm transition flex items-center">
                                        👁 Ver
                                    </a>
                                    
                                    <!-- Enlace para Editar -->
                                    <a href="{{ route('proveedores.edit', $proveedor->id) }}" 
                                       class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition flex items-center">
                                        ✏ Editar
                                    </a>
                                    
                                    <!-- Formulario para Eliminar -->
                                    <form action="{{ route('proveedores.destroy', $proveedor->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition flex items-center"
                                                onclick="return confirm('¿Estás seguro de eliminar al proveedor {{ $proveedor->empresa }}?')">
                                            🗑 Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <p class="text-lg">No hay proveedores registrados.</p>
                                    <a href="{{ route('proveedores.create') }}" class="text-blue-500 hover:text-blue-600 mt-2">
                                        Crear el primer proveedor
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .bg-blue-50 { background-color: #eff6ff; }
    .bg-green-50 { background-color: #f0fdf4; }
    .bg-purple-50 { background-color: #faf5ff; }
    .bg-orange-50 { background-color: #fff7ed; }
</style>
@endsection