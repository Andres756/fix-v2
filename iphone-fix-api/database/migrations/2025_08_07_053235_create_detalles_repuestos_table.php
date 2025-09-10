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
        Schema::create('detalles_repuestos', function (Blueprint $table) {
            $table->unsignedInteger('inventario_id')->primary();
            $table->string('modelo_compatible', 100)->nullable();
            $table->string('tipo_repuesto', 100)->nullable();
            $table->string('referencia_fabricante', 100)->nullable();
            $table->integer('garantia_meses')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalles_repuestos');
    }
};
