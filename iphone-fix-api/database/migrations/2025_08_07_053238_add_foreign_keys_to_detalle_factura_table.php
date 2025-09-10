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
        Schema::table('detalle_factura', function (Blueprint $table) {
            $table->foreign(['factura_id'], 'detalle_factura_ibfk_1')->references(['id'])->on('facturas');
            $table->foreign(['tipo_item_id'], 'detalle_factura_ibfk_2')->references(['id'])->on('tipos_item_factura');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detalle_factura', function (Blueprint $table) {
            $table->dropForeign('detalle_factura_ibfk_1');
            $table->dropForeign('detalle_factura_ibfk_2');
        });
    }
};
