Descripción del Sistema
El Sistema de Inventario para Tienda es una aplicación web desarrollada con Laravel 10 que permite gestionar de manera eficiente el inventario de una tienda. El sistema incluye módulos completos para el control de productos, proveedores, ventas y reportes, con características avanzadas como control de stock, códigos de barras y seguimiento de precios.

Características Principales
- Gestión de Productos: CRUD completo con control de stock mínimo/máximo
- Códigos de Barras: Generación y gestión automática de códigos de barras
- Control de Proveedores: Registro y gestión de empresas proveedoras
- Sistema de Ventas: Proceso completo de facturación con items y totales
- Historial de Precios: Seguimiento de cambios en precios de productos
- Reportes PDF: Generación de reportes en formato PDF con DomPDF
- Interfaz Responsive: Diseño moderno con Tailwind CSS

Modulos del sistema
- Productos: Código, nombre, categoría, stock, foto, control de stock mínimo/máximo, códigos de barras, historial de precios
- Proveedores: Empresa, contacto, productos, gestión de relaciones
- Ventas: Facturación completa, items de venta, cálculo automático de totales
- Reportes: Productos más vendidos, reportes PDF, estadísticas de inventario

Tecnologías Utilizadas
- Backend: Laravel 10+ (Arquitectura monolítica)
- Base de Datos: MySQL
- JavaScript: Vanilla (sin frameworks adicionales)
- Reportes: PDF con DomPDF
- Códigos de Barras: Generación automática

 Instalación en Linux
 Prerrequisitos:
 - PHP 8.1 o superior
 - Composer
 - MySQL 5.7 o superior
 - Node.js (opcional, para assets)
 - Servidor web (Apache/Nginx)

 Pasos de Instalación
 1. Clonar o descargar el proyecto
 git clone [url-del-repositorio]
 cd sistema-inventario
 2. Instalar dependencias de PHP
 composer install
 3. Configurar variables de entorno
 cp .env.example .env
 php artisan key:generate
 4. Crear una base de datos MySQL llamada sistema_inventario
Configurar las credenciales en el archivo .env:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_inventario
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
5. Ejecutar migraciones y seeders
php artisan migrate --seed
6. Configurar almacenamiento
php artisan storage:link
7. Instalar dependencias de frontend (opcional)
npm install
npm run build
8. Configurar permisos
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

Estructura del Proyecto
sistema-inventario/
├── app/
│   ├── Console/Commands/
│   │   ├── GenerarBarCodeProducto.php
│   │   └── GenerarCodigosBarra.php
│   ├── Http/Controllers/
│   │   ├── CategoriaController.php
│   │   ├── ProductoController.php
│   │   ├── ProveedorController.php
│   │   ├── ReporteController.php
│   │   └── VentaController.php
│   └── Models/
│       ├── Categoria.php
│       ├── HistorialPrecio.php
│       ├── Producto.php
│       ├── Proveedor.php
│       ├── User.php
│       ├── Venta.php
│       └── VentaDetalle.php
├── database/migrations/
│   ├── Create_productos_table.php
│   ├── Create_proveedores_table.php
│   ├── Create_ventas_table.php
│   └── Create_historial_precios_table.php
└── resources/views/
    ├── productos/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   ├── edit.blade.php
    │   └── show.blade.php
    ├── proveedores/
    ├── ventas/
    └── reportes/

    Características Únicas Implementadas
    Control de Stock:
     - Alertas de stock mínimo/máximo
     - Gestión automática de inventario
     - Seguimiento en tiempo real
    Códigos de Barras:
     - Generación automática de códigos
     - Escaneo y búsqueda por código
     - Integración con sistemas de punto de venta
    Historial de Precios
      - Registro completo de cambios de precios
      - Gráficos de evolución de precios
      - Reportes de fluctuaciones
    Reportes Avanzados
      - Productos más vendidos
      - Estadísticas de ventas por período
      - Reportes de inventario en PDF

    Integrantes del Grupo 
    Cristhian Robles: Desarrollador Backend, Laravel, MySQL
    Miguel Madariaga: Desarrollador Full-Stack, Laravel, Blade, JavaScript
    Dani Carvajal: Desarrollador Frontend, Tailwind CSS, UX/UI

## 📸 Capturas de Pantalla

### Dashboard Principal
![Captura del Sistema](screenshots/capture-sistema.png)
### Modulo proveedores
![Captura del Sistema](screenshots/proveedores.png)
