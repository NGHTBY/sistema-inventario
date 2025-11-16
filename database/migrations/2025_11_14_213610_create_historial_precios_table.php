<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('historial_precios')) {
            Schema::create('historial_precios', function (Blueprint $table) {
                $table->id();
                $table->foreignId('producto_id')->constrained()->onDelete('cascade');
                $table->decimal('precio_anterior', 10, 2);
                $table->decimal('precio_nuevo', 10, 2);
                $table->decimal('diferencia', 10, 2);
                $table->decimal('porcentaje_cambio', 5, 2);
                $table->string('motivo')->nullable();
                $table->timestamp('fecha_cambio');
                $table->timestamps();
                
                $table->index(['producto_id', 'fecha_cambio']);
                $table->index('fecha_cambio');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('historial_precios');
    }
};