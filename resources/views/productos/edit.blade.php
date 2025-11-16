@php
use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6 max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Editar Producto</h2>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('productos.update', $producto) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Columna Izquierda -->
                <div class="space-y-4">
                    <!-- Código -->
                    <div>
                        <label for="codigo" class="block text-sm font-medium text-gray-700">Código</label>
                        <input type="text" name="codigo" id="codigo" 
                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('codigo', $producto->codigo) }}">
                        @error('codigo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Código de Barras -->
                    <div>
                        <label for="codigo_barras" class="block text-sm font-medium text-gray-700">Código de Barras</label>
                        <input type="text" name="codigo_barras" id="codigo_barras" 
                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('codigo_barras', $producto->codigo_barras) }}">
                        
                        <!-- Mostrar código de barras actual -->
                        @if($producto->barcode_image_url)
                            <div class="mt-2 p-2 bg-gray-50 rounded border">
                                <p class="text-xs text-gray-600 mb-1">Código de barras actual:</p>
                                <div class="flex items-center space-x-2">
                                    <img src="{{ $producto->barcode_image_url }}" alt="Código de barras" class="h-8">
                                    <span class="text-sm font-mono">{{ $producto->codigo_barras }}</span>
                                </div>
                            </div>
                        @endif
                        
                        @error('codigo_barras')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nombre -->
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre *</label>
                        <input type="text" name="nombre" id="nombre" required
                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('nombre', $producto->nombre) }}">
                        @error('nombre')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Categoría -->
                    <div>
                        <label for="categoria" class="block text-sm font-medium text-gray-700">Categoría *</label>
                        <input type="text" name="categoria" id="categoria" required
                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('categoria', $producto->categoria) }}"
                               placeholder="Ej: Electrónicos, Ropa, Hogar...">
                        @error('categoria')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Proveedor -->
                    <div>
                        <label for="proveedor_id" class="block text-sm font-medium text-gray-700">Proveedor</label>
                        <select name="proveedor_id" id="proveedor_id"
                                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Seleccionar proveedor</option>
                            @foreach($proveedores as $proveedor)
                                <option value="{{ $proveedor->id }}" {{ old('proveedor_id', $producto->proveedor_id) == $proveedor->id ? 'selected' : '' }}>
                                    {{ $proveedor->empresa }} - {{ $proveedor->contacto }}
                                </option>
                            @endforeach
                        </select>
                        @error('proveedor_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Columna Derecha -->
                <div class="space-y-4">
                    <!-- Precio Compra -->
                    <div>
                        <label for="precio_compra" class="block text-sm font-medium text-gray-700">Precio Compra *</label>
                        <input type="number" name="precio_compra" id="precio_compra" step="0.01" min="0" required
                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('precio_compra', $producto->precio_compra) }}">
                        @error('precio_compra')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Precio Venta -->
                    <div>
                        <label for="precio_venta" class="block text-sm font-medium text-gray-700">Precio Venta *</label>
                        <input type="number" name="precio_venta" id="precio_venta" step="0.01" min="0" required
                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('precio_venta', $producto->precio_venta) }}">
                        @error('precio_venta')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stock -->
                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700">Stock *</label>
                        <input type="number" name="stock" id="stock" min="0" required
                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('stock', $producto->stock) }}">
                        @error('stock')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Foto -->
                    <div>
                        <label for="foto" class="block text-sm font-medium text-gray-700">Imagen del Producto</label>
                        
                        @if($producto->foto)
                            <div class="mb-2">
                                <p class="text-sm text-gray-600">Imagen actual:</p>
                                <img src="{{ asset('storage/' . $producto->foto) }}" 
                                     alt="Imagen actual" 
                                     class="w-32 h-32 object-cover rounded-lg border mt-1">
                            </div>
                        @endif
                        
                        <input type="file" name="foto" id="foto" accept="image/*"
                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        
                        <!-- Vista previa de nueva imagen -->
                        <div id="image-preview-container" class="hidden mt-2">
                            <p class="text-sm text-gray-600 mb-1">Vista previa nueva imagen:</p>
                            <img id="image-preview" class="w-32 h-32 object-cover rounded-lg border">
                        </div>
                        
                        @error('foto')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Stock Mínimo y Máximo -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="stock_minimo" class="block text-sm font-medium text-gray-700">Stock Mínimo *</label>
                    <input type="number" name="stock_minimo" id="stock_minimo" min="0" required
                           class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('stock_minimo', $producto->stock_minimo) }}">
                    @error('stock_minimo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="stock_maximo" class="block text-sm font-medium text-gray-700">Stock Máximo *</label>
                    <input type="number" name="stock_maximo" id="stock_maximo" min="0" required
                           class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('stock_maximo', $producto->stock_maximo) }}">
                    @error('stock_maximo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Descripción -->
            <div class="mb-6">
                <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="3"
                          class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('descripcion', $producto->descripcion) }}</textarea>
                @error('descripcion')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('productos.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Cancelar
                </a>
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Actualizar Producto
                </button>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
    // Vista previa de imagen
    document.getElementById('foto').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const previewContainer = document.getElementById('image-preview-container');
        const preview = document.getElementById('image-preview');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                previewContainer.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        } else {
            previewContainer.classList.add('hidden');
        }
    });
</script>
@endsection
@endsection