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
        Schema::table('entradas_producto', function (Blueprint $table) {
            $table->foreign(['inventario_id'], 'entradas_producto_ibfk_1')->references(['id'])->on('inventarios');
            $table->foreign(['motivo_ingreso_id'], 'entradas_producto_ibfk_3')->references(['id'])->on('motivos_ingreso');
            $table->foreign(['lote_id'], 'entradas_producto_ibfk_2')->references(['id'])->on('lotes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entradas_producto', function (Blueprint $table) {
            $table->dropForeign('entradas_producto_ibfk_1');
            $table->dropForeign('entradas_producto_ibfk_3');
            $table->dropForeign('entradas_producto_ibfk_2');
        });
    }
};
