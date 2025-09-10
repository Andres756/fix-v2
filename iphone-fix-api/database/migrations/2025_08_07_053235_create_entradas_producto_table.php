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
        Schema::create('entradas_producto', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('inventario_id')->nullable()->index('inventario_id');
            $table->unsignedInteger('lote_id')->nullable()->index('lote_id');
            $table->unsignedInteger('motivo_ingreso_id')->nullable()->index('motivo_ingreso_id');
            $table->integer('cantidad');
            $table->date('fecha_entrada')->nullable();
            $table->text('notas')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entradas_producto');
    }
};
