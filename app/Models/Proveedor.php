<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'empresa',
        'contacto',
        'email',
        'telefono',
        'direccion',
        'nit',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}