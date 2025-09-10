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
        Schema::table('historial_estados_orden', function (Blueprint $table) {
            $table->foreign(['orden_id'], 'historial_estados_orden_ibfk_1')->references(['id'])->on('ordenes_servicio');
            $table->foreign(['estado_nuevo_id'], 'historial_estados_orden_ibfk_3')->references(['id'])->on('estados_orden_servicio');
            $table->foreign(['estado_anterior_id'], 'historial_estados_orden_ibfk_2')->references(['id'])->on('estados_orden_servicio');
            $table->foreign(['user_id'], 'historial_estados_orden_ibfk_4')->references(['id'])->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('historial_estados_orden', function (Blueprint $table) {
            $table->dropForeign('historial_estados_orden_ibfk_1');
            $table->dropForeign('historial_estados_orden_ibfk_3');
            $table->dropForeign('historial_estados_orden_ibfk_2');
            $table->dropForeign('historial_estados_orden_ibfk_4');
        });
    }
};
