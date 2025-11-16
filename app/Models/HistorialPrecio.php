<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HistorialPrecio extends Model
{
    use HasFactory;

    protected $table = 'historial_precios';

    protected $fillable = [
        'producto_id',
        'precio_anterior',
        'precio_nuevo',
        'diferencia',
        'porcentaje_cambio',
        'motivo',
        'fecha_cambio'
    ];

    protected $casts = [
        'precio_anterior' => 'decimal:2',
        'precio_nuevo' => 'decimal:2',
        'diferencia' => 'decimal:2',
        'porcentaje_cambio' => 'decimal:2',
        'fecha_cambio' => 'datetime'
    ];

    /**
     * Relación con el producto
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Scope para obtener cambios recientes
     */
    public function scopeRecientes($query, $dias = 30)
    {
        return $query->where('fecha_cambio', '>=', now()->subDays($dias));
    }

    /**
     * Scope para obtener aumentos de precio
     */
    public function scopeAumentos($query)
    {
        return $query->where('precio_nuevo', '>', DB::raw('precio_anterior'));
    }

    /**
     * Scope para obtener disminuciones de precio
     */
    public function scopeDisminuciones($query)
    {
        return $query->where('precio_nuevo', '<', DB::raw('precio_anterior'));
    }

    /**
     * Obtener el tipo de cambio (aumento/disminución)
     */
    public function getTipoCambioAttribute()
    {
        if ($this->precio_nuevo > $this->precio_anterior) {
            return 'aumento';
        } elseif ($this->precio_nuevo < $this->precio_anterior) {
            return 'disminución';
        } else {
            return 'sin cambio';
        }
    }

    /**
     * Obtener el color según el tipo de cambio
     */
    public function getColorCambioAttribute()
    {
        return match($this->tipo_cambio) {
            'aumento' => 'text-red-600',
            'disminución' => 'text-green-600',
            default => 'text-gray-600'
        };
    }

    /**
     * Obtener el icono según el tipo de cambio
     */
    public function getIconoCambioAttribute()
    {
        return match($this->tipo_cambio) {
            'aumento' => '📈',
            'disminución' => '📉',
            default => '➡️'
        };
    }
}