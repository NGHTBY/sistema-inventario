<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\DNS1D; // Agregar este use

class GenerarCodigosBarras extends Command
{
    protected $signature = 'productos:generar-barcodes';
    protected $description = 'Generar códigos de barras para todos los productos';

    public function handle()
    {
        $productos = Producto::all();
        
        $this->info("Iniciando generación de códigos de barras para {$productos->count()} productos...");
        
        // Crear instancia de DNS1D
        $dns1d = new DNS1D();
        
        foreach ($productos as $producto) {
            $this->info("Procesando: {$producto->nombre} (ID: {$producto->id})");
            
            // Generar código de barras numérico si no existe
            if (empty($producto->codigo_barras)) {
                $producto->codigo_barras = 'PROD' . str_pad($producto->id, 6, '0', STR_PAD_LEFT);
                $this->info("  Código generado: {$producto->codigo_barras}");
            }
            
            // Generar imagen del código de barras
            try {
                // Usar la instancia en lugar del método estático
                $barcode = $dns1d->getBarcodeSVG($producto->codigo_barras, 'C128', 2, 33);
                
                $filename = 'barcodes/' . $producto->codigo_barras . '.svg';
                $guardado = Storage::disk('public')->put($filename, $barcode);
                
                if ($guardado) {
                    $producto->codigo_barras_imagen = $filename;
                    $producto->save();
                    $this->info("  ✅ Imagen SVG guardada: {$filename}");
                } else {
                    $this->error("  ❌ No se pudo guardar la imagen SVG");
                }
                
            } catch (\Exception $e) {
                $this->error("  ❌ Error: " . $e->getMessage());
                
                // Intentar método alternativo
                try {
                    $barcodePNG = $dns1d->getBarcodePNG($producto->codigo_barras, 'C128', 2, 33);
                    $filename = 'barcodes/' . $producto->codigo_barras . '.png';
                    
                    // El PNG viene en base64, necesitamos decodificarlo
                    $imageData = base64_decode($barcodePNG);
                    $guardado = Storage::disk('public')->put($filename, $imageData);
                    
                    if ($guardado) {
                        $producto->codigo_barras_imagen = $filename;
                        $producto->save();
                        $this->info("  ✅ Imagen PNG guardada: {$filename}");
                    }
                    
                } catch (\Exception $e2) {
                    $this->error("  ❌ Error alternativo: " . $e2->getMessage());
                }
            }
        }
        
        $this->info("¡Proceso completado!");
    }
}