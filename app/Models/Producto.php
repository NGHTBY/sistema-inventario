<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'codigo_barras',
        'codigo_barras_imagen',
        'nombre',
        'categoria',
        'descripcion',
        'precio_compra',
        'precio_venta',
        'stock',
        'stock_minimo',
        'stock_maximo',
        'foto',
        'proveedor_id',
        'activo'
    ];

    protected $casts = [
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'stock' => 'integer',
        'stock_minimo' => 'integer',
        'stock_maximo' => 'integer',
        'activo' => 'boolean'
    ];

    protected $appends = ['barcode_image_url'];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function ventaDetalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function historialPrecios()
    {
        return $this->hasMany(HistorialPrecio::class)->latest('fecha_cambio');
    }

    public function getStockBajoAttribute()
    {
        return $this->stock <= $this->stock_minimo;
    }

    public function getStockAltoAttribute()
    {
        return $this->stock >= $this->stock_maximo;
    }

    public function getEstadoStockAttribute()
    {
        if ($this->stock == 0) {
            return 'agotado';
        } elseif ($this->stock_bajo) {
            return 'bajo';
        } elseif ($this->stock_alto) {
            return 'alto';
        }
        return 'normal';
    }

    public function generarCodigoBarras()
    {
        if (empty($this->codigo_barras)) {
            // SOLUCIÓN: Usar solo números con longitud fija de 8 dígitos
            $this->codigo_barras = str_pad($this->id, 8, '0', STR_PAD_LEFT);
            $this->save();
        }
        return $this->codigo_barras;
    }

    public function generarImagenCodigoBarras()
    {
        // Asegurarse de que el código de barras esté generado
        $codigo = $this->generarCodigoBarras();
        
        Log::info("🔴 INICIANDO generación de imagen para: " . $codigo);
        
        try {
            // Crear directorio si no existe
            if (!Storage::disk('public')->exists('barcodes')) {
                Storage::disk('public')->makeDirectory('barcodes');
            }

            // Usar DNS1D directamente
            $dns1d = new \Milon\Barcode\DNS1D();
            
            // SOLUCIÓN: Parámetros consistentes para todos los códigos de barras
            // Usar CODE128 que es más compacto y mantener el mismo tamaño para todos
            $barcodePNG = $dns1d->getBarcodePNG($codigo, 'C128', 2, 50); // Altura fija de 50px
            
            if ($barcodePNG) {
                $filename = 'barcodes/' . $codigo . '.png';
                
                // Decodificar base64 y guardar
                $imageData = base64_decode($barcodePNG);
                $guardado = Storage::disk('public')->put($filename, $imageData);
                
                if ($guardado) {
                    Log::info("✅ IMAGEN PNG GUARDADA: " . $filename);
                    $this->codigo_barras_imagen = $filename;
                    $this->save();
                    return $filename;
                } else {
                    Log::error("❌ NO SE PUDO GUARDAR EL ARCHIVO PNG: " . $filename);
                }
            } else {
                Log::error("❌ NO SE PUDO GENERAR EL CÓDIGO DE BARRAS PNG");
            }
            
        } catch (\Exception $e) {
            Log::error('❌ ERROR generando código de barras: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
        }
        
        Log::error("❌ TODOS LOS MÉTODOS FALLARON para: " . $codigo);
        return null;
    }

    public function getBarcodeImageUrlAttribute()
    {
        if ($this->codigo_barras_imagen && Storage::disk('public')->exists($this->codigo_barras_imagen)) {
            return asset('storage/' . $this->codigo_barras_imagen);
        }
        
        // Si no existe la imagen, generar una en tiempo real
        if ($this->codigo_barras) {
            try {
                $dns1d = new \Milon\Barcode\DNS1D();
                // SOLUCIÓN: Mismo tamaño para códigos en tiempo real
                $barcodePNG = $dns1d->getBarcodePNG($this->codigo_barras, 'C128', 2, 50);
                if ($barcodePNG) {
                    return 'data:image/png;base64,' . $barcodePNG;
                }
            } catch (\Exception $e) {
                Log::error('Error generando barcode en tiempo real: ' . $e->getMessage());
            }
        }
        
        return null;
    }

    public function registrarCambioPrecio($precioAnterior, $precioNuevo, $motivo = null)
    {
        // CORRECCIÓN: Validar que precio_anterior no sea NULL
        if ($precioAnterior === null) {
            $precioAnterior = 0.00;
        }
        
        if ($precioAnterior != $precioNuevo) {
            $diferencia = $precioNuevo - $precioAnterior;
            $porcentaje = $precioAnterior > 0 ? (($diferencia / $precioAnterior) * 100) : 100;

            return HistorialPrecio::create([
                'producto_id' => $this->id,
                'precio_anterior' => $precioAnterior,
                'precio_nuevo' => $precioNuevo,
                'diferencia' => $diferencia,
                'porcentaje_cambio' => $porcentaje,
                'motivo' => $motivo,
                'fecha_cambio' => now()
            ]);
        }
        return null;
    }

    public function ultimoCambioPrecio()
    {
        return $this->historialPrecios()->first();
    }

    public function estadisticasPrecios($dias = 30)
    {
        $cambiosRecientes = $this->historialPrecios()
            ->where('fecha_cambio', '>=', now()->subDays($dias))
            ->get();
        
        return [
            'total_cambios' => $cambiosRecientes->count(),
            'aumentos' => $cambiosRecientes->where('diferencia', '>', 0)->count(),
            'disminuciones' => $cambiosRecientes->where('diferencia', '<', 0)->count(),
            'cambio_promedio' => $cambiosRecientes->avg('porcentaje_cambio'),
            'ultimo_cambio' => $cambiosRecientes->first()
        ];
    }

    // Scope para productos con stock bajo
    public function scopeStockBajo($query)
    {
        return $query->whereRaw('stock <= stock_minimo');
    }

    // Scope para productos activos
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    // Actualizar stock
    public function actualizarStock($cantidad, $tipo = 'venta')
    {
        if ($tipo === 'venta') {
            $this->stock -= $cantidad;
        } else {
            $this->stock += $cantidad;
        }
        
        $this->save();
        return $this;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($producto) {
            if (empty($producto->codigo)) {
                $producto->codigo = 'PROD-' . strtoupper(uniqid());
            }
        });

        static::created(function ($producto) {
            Log::info("🎯 EVENTO CREATED ejecutado para: " . $producto->nombre);
            // Generar código de barras automáticamente al crear producto
            $producto->generarCodigoBarras();
            // Generar imagen en segundo plano
            dispatch(function () use ($producto) {
                $producto->generarImagenCodigoBarras();
            });
            Log::info("🎯 PROCESO COMPLETADO para: " . $producto->nombre);
        });

        static::updated(function ($producto) {
            // CORRECCIÓN: Validar que el precio anterior no sea NULL
            if ($producto->isDirty('precio_venta')) {
                $precioAnterior = $producto->getOriginal('precio_venta');
                $precioNuevo = $producto->precio_venta;
                
                // Si es la primera vez que se establece el precio, precio_anterior será NULL
                if ($precioAnterior === null) {
                    $precioAnterior = 0.00;
                }
                
                $producto->registrarCambioPrecio(
                    $precioAnterior, 
                    $precioNuevo, 
                    'Actualización automática'
                );
            }
        });
    }
}