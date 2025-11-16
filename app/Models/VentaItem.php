<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaItem extends Model
{
    use HasFactory;

    // CORREGIDO: Usar nombres reales de la BD
    protected $fillable = [
        'venta_id',
        'producto_id',
        'cantidad',
        'precio', // ← CORREGIDO: Cambiado de 'precio_unitario' a 'precio'
        'subtotal'
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    // Accesor para compatibilidad
    public function getPrecioUnitarioAttribute()
    {
        return $this->precio;
    }
}