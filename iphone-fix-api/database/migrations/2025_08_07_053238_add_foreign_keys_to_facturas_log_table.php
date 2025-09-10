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
        Schema::table('facturas_log', function (Blueprint $table) {
            $table->foreign(['factura_id'], 'facturas_log_ibfk_1')->references(['id'])->on('facturas');
            $table->foreign(['usuario_id'], 'facturas_log_ibfk_3')->references(['id'])->on('users');
            $table->foreign(['accion_id'], 'facturas_log_ibfk_2')->references(['id'])->on('acciones_factura_log');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('facturas_log', function (Blueprint $table) {
            $table->dropForeign('facturas_log_ibfk_1');
            $table->dropForeign('facturas_log_ibfk_3');
            $table->dropForeign('facturas_log_ibfk_2');
        });
    }
};
