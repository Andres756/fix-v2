<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalles_equipos', function (Blueprint $table) {
            $table->unsignedInteger('inventario_id')->primary();
            $table->string('imei_1', 100);
            $table->string('imei_2', 100)->nullable();
            $table->string('estado_fisico', 50)->nullable();
            $table->string('version_ios', 50)->nullable();
            $table->string('almacenamiento', 50)->nullable();
            $table->string('color', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalles_equipos');
    }
};
