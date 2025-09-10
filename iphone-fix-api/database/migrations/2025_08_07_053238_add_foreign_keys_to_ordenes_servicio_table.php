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
        Schema::table('ordenes_servicio', function (Blueprint $table) {
            $table->foreign(['cliente_id'], 'ordenes_servicio_ibfk_1')->references(['id'])->on('clientes');
            $table->foreign(['tecnico_id'], 'ordenes_servicio_ibfk_3')->references(['id'])->on('users');
            $table->foreign(['equipo_id'], 'ordenes_servicio_ibfk_2')->references(['id'])->on('equipos_servicio');
            $table->foreign(['estado_id'], 'ordenes_servicio_ibfk_4')->references(['id'])->on('estados_orden_servicio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ordenes_servicio', function (Blueprint $table) {
            $table->dropForeign('ordenes_servicio_ibfk_1');
            $table->dropForeign('ordenes_servicio_ibfk_3');
            $table->dropForeign('ordenes_servicio_ibfk_2');
            $table->dropForeign('ordenes_servicio_ibfk_4');
        });
    }
};
