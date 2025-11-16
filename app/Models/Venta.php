<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    // CORREGIDO: Usar los nombres reales de la base de datos
    protected $fillable = [
        'factura',      // ← Cambiado de numero_factura
        'total',
        'fecha'         // ← Cambiado de fecha_venta
        // Quitar 'cliente' y 'user_id' si no existen en tu BD
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'fecha' => 'datetime'  // ← Cambiado de fecha_venta
    ];

    /**
     * Relación con los items de venta - USAR 'detalles' para consistencia
     */
    public function detalles()
    {
        return $this->hasMany(VentaItem::class);
    }

    /**
     * Accesor para compatibilidad
     */
    public function getNumeroFacturaAttribute()
    {
        return $this->factura;
    }

    /**
     * Generar número de factura automáticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($venta) {
            if (empty($venta->factura)) {
                $ultimaVenta = static::latest()->first();
                $nuevoId = $ultimaVenta ? $ultimaVenta->id + 1 : 1;
                $venta->factura = 'FAC-' . str_pad($nuevoId, 6, '0', STR_PAD_LEFT);
            }
            if (empty($venta->fecha)) {
                $venta->fecha = now();
            }
        });
    }
}