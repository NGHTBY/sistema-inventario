@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold">🛒 Registrar Nueva Venta</h1>
                    <p class="text-blue-100 mt-1">Complete la información de la venta</p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold" id="totalDisplay">$0</div>
                    <div class="text-blue-100 text-sm">Total de la venta</div>
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Alertas -->
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <strong class="font-bold">Error!</strong>
                    <ul class="list-disc pl-5 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <form id="ventaForm" action="{{ route('ventas.store') }}" method="POST">
                @csrf

                <!-- Información del Cliente -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="cliente" class="block text-sm font-medium text-gray-700 mb-2">
                            👤 Cliente *
                        </label>
                        <input type="text" name="cliente" id="cliente" 
                               value="{{ old('cliente') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                               placeholder="Nombre del cliente" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            📄 Número de Factura
                        </label>
                        <input type="text" value="FAC-{{ date('Ymd-His') }}-{{ rand(100, 999) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-gray-50 text-gray-600 cursor-not-allowed"
                               readonly>
                    </div>
                </div>

                <!-- Agregar Productos -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">➕ Agregar Productos</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                        <div class="md:col-span-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Producto</label>
                            <select id="productoSelect" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                <option value="">-- Selecciona un producto --</option>
                                @foreach ($productos as $producto)
                                    <option value="{{ $producto->id }}" 
                                            data-nombre="{{ $producto->nombre }}"
                                            data-precio="{{ $producto->precio_venta }}"
                                            data-stock="{{ $producto->stock }}"
                                            data-codigo="{{ $producto->codigo }}"
                                            class="{{ $producto->stock == 0 ? 'text-red-500' : '' }}">
                                        {{ $producto->nombre }} 
                                        @if($producto->stock > 0)
                                            (Stock: {{ $producto->stock }}) - ${{ number_format($producto->precio_venta, 0) }}
                                        @else
                                            - ❌ AGOTADO
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Precio Unitario</label>
                            <input type="text" id="precioInput" 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-gray-50 text-gray-600 cursor-not-allowed"
                                   readonly>
                        </div>

                        <div class="md:col-span-2">
                            <label for="cantidadInput" class="block text-sm font-medium text-gray-700 mb-2">Cantidad</label>
                            <input type="number" id="cantidadInput" min="1" value="1"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                        </div>

                        <div class="md:col-span-2">
                            <button type="button" id="agregarBtn" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 shadow-md hover:shadow-lg">
                                ➕ Agregar
                            </button>
                        </div>
                    </div>

                    <!-- Información del producto seleccionado -->
                    <div id="productoInfo" class="mt-4 hidden">
                        <div class="bg-white rounded-lg border border-blue-200 p-4 shadow-sm">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-semibold text-gray-700">Stock disponible:</span>
                                    <span id="stockDisponible" class="ml-2 font-bold"></span>
                                </div>
                                <div>
                                    <span class="font-semibold text-gray-700">Código:</span>
                                    <span id="codigoProducto" class="ml-2 font-mono text-blue-600"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Productos Agregados -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">📋 Productos en la Venta</h3>
                    
                    <div class="overflow-hidden rounded-lg border border-gray-200 shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Producto</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase">Cantidad</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase">Precio Unit.</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase">Subtotal</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaItems" class="bg-white divide-y divide-gray-200">
                                <!-- Los productos se agregarán aquí dinámicamente -->
                                <tr id="emptyMessage">
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                            <p class="text-gray-600">No hay productos agregados</p>
                                            <p class="text-sm text-gray-400 mt-1">Agrega productos usando el formulario superior</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right font-semibold text-gray-700">Total General:</td>
                                    <td colspan="2" class="px-6 py-4 text-center font-bold text-xl text-green-600" id="totalVenta">
                                        $0
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('ventas.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-8 rounded-lg transition duration-200 shadow-md hover:shadow-lg">
                        ↩ Cancelar
                    </a>
                    <button type="submit" id="submitBtn" disabled
                            class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-semibold py-3 px-8 rounded-lg transition duration-200 shadow-md hover:shadow-lg flex items-center">
                        💾 Guardar Venta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productoSelect = document.getElementById('productoSelect');
    const cantidadInput = document.getElementById('cantidadInput');
    const precioInput = document.getElementById('precioInput');
    const agregarBtn = document.getElementById('agregarBtn');
    const tablaItems = document.getElementById('tablaItems');
    const totalVenta = document.getElementById('totalVenta');
    const totalDisplay = document.getElementById('totalDisplay');
    const productoInfo = document.getElementById('productoInfo');
    const stockDisponible = document.getElementById('stockDisponible');
    const codigoProducto = document.getElementById('codigoProducto');
    const submitBtn = document.getElementById('submitBtn');
    const emptyMessage = document.getElementById('emptyMessage');

    let productosAgregados = [];
    let contador = 0;

    // Formatear números en formato colombiano
    function formatoColombiano(numero) {
        return new Intl.NumberFormat('es-CO').format(numero);
    }

    // Actualizar información del producto seleccionado
    productoSelect.addEventListener('change', function() {
        const option = this.selectedOptions[0];
        if (option && option.value) {
            const precio = parseFloat(option.dataset.precio);
            const stock = parseInt(option.dataset.stock);
            const codigo = option.dataset.codigo;

            precioInput.value = '$' + formatoColombiano(precio);
            
            // Mostrar información del producto
            stockDisponible.textContent = stock;
            if (stock > 10) {
                stockDisponible.className = 'text-green-600 font-bold';
            } else if (stock > 0) {
                stockDisponible.className = 'text-yellow-600 font-bold';
            } else {
                stockDisponible.className = 'text-red-600 font-bold';
            }
            
            codigoProducto.textContent = codigo;
            productoInfo.classList.remove('hidden');

            // Establecer cantidad máxima
            cantidadInput.max = stock;
            if (parseInt(cantidadInput.value) > stock) {
                cantidadInput.value = stock;
            }

            // Habilitar botón de agregar si hay stock
            agregarBtn.disabled = stock === 0;
        } else {
            productoInfo.classList.add('hidden');
            precioInput.value = '';
            agregarBtn.disabled = false;
        }
    });

    // Agregar producto a la venta
    agregarBtn.addEventListener('click', function() {
        const productoId = productoSelect.value;
        const option = productoSelect.selectedOptions[0];
        
        if (!productoId) {
            alert('Por favor selecciona un producto.');
            return;
        }

        const nombre = option.dataset.nombre;
        const precio = parseFloat(option.dataset.precio);
        const stock = parseInt(option.dataset.stock);
        const cantidad = parseInt(cantidadInput.value) || 1;

        // Validaciones
        if (cantidad < 1) {
            alert('La cantidad debe ser mayor a 0.');
            return;
        }

        if (cantidad > stock) {
            alert(`Stock insuficiente. Solo hay ${stock} unidades disponibles.`);
            return;
        }

        // Verificar si el producto ya fue agregado
        const productoExistente = productosAgregados.find(p => p.id === productoId);
        if (productoExistente) {
            alert('Este producto ya fue agregado a la venta.');
            return;
        }

        // Agregar producto
        const producto = {
            id: productoId,
            nombre: nombre,
            precio: precio,
            cantidad: cantidad,
            subtotal: precio * cantidad
        };

        productosAgregados.push(producto);
        actualizarTabla();
        actualizarTotal();

        // Limpiar formulario
        productoSelect.value = '';
        cantidadInput.value = 1;
        precioInput.value = '';
        productoInfo.classList.add('hidden');
        agregarBtn.disabled = false;
    });

    // Actualizar tabla de productos
    function actualizarTabla() {
        // Limpiar tabla excepto el mensaje vacío
        const rows = tablaItems.querySelectorAll('tr:not(#emptyMessage)');
        rows.forEach(row => row.remove());
        
        if (productosAgregados.length === 0) {
            emptyMessage.classList.remove('hidden');
            return;
        }

        emptyMessage.classList.add('hidden');

        productosAgregados.forEach((producto, index) => {
            const fila = document.createElement('tr');
            fila.className = 'hover:bg-gray-50 transition duration-150';
            fila.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="font-medium text-gray-900">${producto.nombre}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                        ${formatoColombiano(producto.cantidad)}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-gray-900 font-semibold">
                    $${formatoColombiano(producto.precio)}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-green-600 font-bold">
                    $${formatoColombiano(producto.subtotal)}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <button type="button" onclick="eliminarProducto(${index})" 
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition duration-200 shadow hover:shadow-md">
                        🗑 Eliminar
                    </button>
                </td>
            `;
            tablaItems.appendChild(fila);
        });

        // Agregar inputs hidden al formulario
        actualizarFormulario();
    }

    // Actualizar total
    function actualizarTotal() {
        const total = productosAgregados.reduce((sum, producto) => sum + producto.subtotal, 0);
        totalVenta.textContent = '$' + formatoColombiano(total);
        totalDisplay.textContent = '$' + formatoColombiano(total);
        
        // Habilitar/deshabilitar botón de enviar
        submitBtn.disabled = productosAgregados.length === 0;
        if (productosAgregados.length > 0) {
            submitBtn.classList.remove('disabled:bg-gray-400', 'disabled:cursor-not-allowed');
            submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
        } else {
            submitBtn.classList.add('disabled:bg-gray-400', 'disabled:cursor-not-allowed');
            submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        }
    }

    // Actualizar formulario con datos
    function actualizarFormulario() {
        // Eliminar inputs anteriores
        document.querySelectorAll('[name^="productos"]').forEach(input => input.remove());

        // Agregar nuevos inputs
        productosAgregados.forEach((producto, index) => {
            agregarInputHidden('productos[' + index + '][id]', producto.id);
            agregarInputHidden('productos[' + index + '][cantidad]', producto.cantidad);
            agregarInputHidden('productos[' + index + '][precio]', producto.precio);
        });
    }

    function agregarInputHidden(name, value) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        document.getElementById('ventaForm').appendChild(input);
    }

    // Función global para eliminar productos
    window.eliminarProducto = function(index) {
        if (confirm('¿Estás seguro de eliminar este producto de la venta?')) {
            productosAgregados.splice(index, 1);
            actualizarTabla();
            actualizarTotal();
        }
    };

    // Validar formulario antes de enviar
    document.getElementById('ventaForm').addEventListener('submit', function(e) {
        if (productosAgregados.length === 0) {
            e.preventDefault();
            alert('Debe agregar al menos un producto a la venta.');
            return;
        }

        const cliente = document.getElementById('cliente').value.trim();
        if (!cliente) {
            e.preventDefault();
            alert('Debe ingresar el nombre del cliente.');
            document.getElementById('cliente').focus();
            return;
        }

        // Mostrar loading
        submitBtn.innerHTML = '⏳ Procesando...';
        submitBtn.disabled = true;
    });

    // Efectos de hover mejorados
    const inputs = document.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('ring-2', 'ring-blue-200');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('ring-2', 'ring-blue-200');
        });
    });
});
</script>

<style>
    .bg-gradient-to-r {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .shadow-md {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .shadow-lg {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    .hover\:shadow-lg:hover {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
</style>
@endsection