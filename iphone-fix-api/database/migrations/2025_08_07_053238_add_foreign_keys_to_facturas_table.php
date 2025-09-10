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
        Schema::table('facturas', function (Blueprint $table) {
            $table->foreign(['cliente_id'], 'facturas_ibfk_1')->references(['id'])->on('clientes');
            $table->foreign(['tipo_venta_id'], 'facturas_ibfk_3')->references(['id'])->on('tipos_venta');
            $table->foreign(['forma_pago_id'], 'facturas_ibfk_5')->references(['id'])->on('formas_pago');
            $table->foreign(['usuario_id'], 'facturas_ibfk_2')->references(['id'])->on('users');
            $table->foreign(['estado_id'], 'facturas_ibfk_4')->references(['id'])->on('estados_factura');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropForeign('facturas_ibfk_1');
            $table->dropForeign('facturas_ibfk_3');
            $table->dropForeign('facturas_ibfk_5');
            $table->dropForeign('facturas_ibfk_2');
            $table->dropForeign('facturas_ibfk_4');
        });
    }
};
