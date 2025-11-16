<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Inventario</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white p-4 flex justify-between">
        <h1 class="text-xl font-bold">Inventario Tienda</h1>
        <div class="space-x-4">
            <a href="{{ route('productos.index') }}" class="hover:text-blue-200 transition duration-200">Productos</a>
            <a href="{{ route('proveedores.index') }}" class="hover:text-blue-200 transition duration-200">Proveedores</a>
            <a href="{{ route('ventas.index') }}" class="hover:text-blue-200 transition duration-200">Ventas</a>
        </div>
    </nav>

    <!-- CONTENEDOR PRINCIPAL PARA MENSAJES -->
    <div class="container mx-auto px-4 py-6">
        <!-- MOSTRAR MENSAJES DE SESIÓN -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 relative" role="alert">
                <strong class="font-bold">¡Éxito!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.style.display='none'" class="absolute top-0 right-0 px-4 py-3">
                    <span class="text-green-700 hover:text-green-900">×</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 relative" role="alert">
                <strong class="font-bold">¡Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
                <button type="button" onclick="this.parentElement.style.display='none'" class="absolute top-0 right-0 px-4 py-3">
                    <span class="text-red-700 hover:text-red-900">×</span>
                </button>
            </div>
        @endif

        @if(session('info'))
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6 relative" role="alert">
                <strong class="font-bold">Información:</strong>
                <span class="block sm:inline">{{ session('info') }}</span>
                <button type="button" onclick="this.parentElement.style.display='none'" class="absolute top-0 right-0 px-4 py-3">
                    <span class="text-blue-700 hover:text-blue-900">×</span>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 relative" role="alert">
                <strong class="font-bold">Por favor corrige los siguientes errores:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" onclick="this.parentElement.style.display='none'" class="absolute top-0 right-0 px-4 py-3">
                    <span class="text-red-700 hover:text-red-900">×</span>
                </button>
            </div>
        @endif

        <!-- CONTENIDO PRINCIPAL -->
        @yield('content')
    </div>

    <!-- SCRIPT PARA AUTO-OCULTAR MENSAJES DESPUÉS DE 5 SEGUNDOS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-ocultar mensajes después de 5 segundos
            setTimeout(function() {
                const alerts = document.querySelectorAll('[role="alert"]');
                alerts.forEach(function(alert) {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500);
                });
            }, 5000);

            // También agregar funcionalidad de cierre a todos los botones de cerrar
            document.querySelectorAll('[role="alert"] button').forEach(function(button) {
                button.addEventListener('click', function() {
                    const alert = this.parentElement;
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500);
                });
            });
        });
    </script>
</body>
</html>