<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;

class GenerarBarcodeProducto extends Command
{
    protected $signature = 'producto:generar-barcode {id}';
    protected $description = 'Generar código de barras para un producto específico';

    public function handle()
    {
        $id = $this->argument('id');
        $producto = Producto::find($id);
        
        if (!$producto) {
            $this->error("Producto con ID {$id} no encontrado");
            return;
        }
        
        $this->info("Procesando: {$producto->nombre}");
        
        // Generar código de barras
        $producto->generarCodigoBarras();
        $this->info("Código generado: {$producto->codigo_barras}");
        
        // Generar imagen
        $imagen = $producto->generarImagenCodigoBarras();
        if ($imagen) {
            $this->info("✅ Imagen guardada: {$imagen}");
        } else {
            $this->error("❌ Error generando imagen");
        }
    }
}