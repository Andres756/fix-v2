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
        Schema::table('inventarios', function (Blueprint $table) {
            $table->foreign(['proveedor_id'], 'fk_proveedor')->references(['id'])->on('proveedores');
            $table->foreign(['lote_id'], 'inventarios_ibfk_2')->references(['id'])->on('lotes');
            $table->foreign(['tipo_inventario_id'], 'inventarios_ibfk_5')->references(['id'])->on('tipos_de_inventario');
            $table->foreign(['categoria_id'], 'inventarios_ibfk_1')->references(['id'])->on('categorias');
            $table->foreign(['estado_inventario_id'], 'inventarios_ibfk_3')->references(['id'])->on('estados_inventario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventarios', function (Blueprint $table) {
            $table->dropForeign('fk_proveedor');
            $table->dropForeign('inventarios_ibfk_2');
            $table->dropForeign('inventarios_ibfk_5');
            $table->dropForeign('inventarios_ibfk_1');
            $table->dropForeign('inventarios_ibfk_3');
        });
    }
};
