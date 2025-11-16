<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Verificar si la tabla categorias existe y si no tiene la columna activo
        if (Schema::hasTable('categorias') && !Schema::hasColumn('categorias', 'activo')) {
            Schema::table('categorias', function (Blueprint $table) {
                $table->boolean('activo')->default(true)->after('descripcion');
            });
            
            // Actualizar todos los registros existentes a activo = true
            \Illuminate\Support\Facades\DB::table('categorias')->update(['activo' => true]);
        }
    }

    public function down()
    {
        if (Schema::hasTable('categorias') && Schema::hasColumn('categorias', 'activo')) {
            Schema::table('categorias', function (Blueprint $table) {
                $table->dropColumn('activo');
            });
        }
    }
};