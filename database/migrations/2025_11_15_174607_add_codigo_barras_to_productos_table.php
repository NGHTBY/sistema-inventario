<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->string('codigo_barras')->nullable()->after('codigo');
            $table->string('codigo_barras_imagen')->nullable()->after('codigo_barras');
        });
    }

    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['codigo_barras', 'codigo_barras_imagen']);
        });
    }
};