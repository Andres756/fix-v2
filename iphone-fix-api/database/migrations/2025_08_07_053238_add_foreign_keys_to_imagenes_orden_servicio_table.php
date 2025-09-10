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
        Schema::table('imagenes_orden_servicio', function (Blueprint $table) {
            $table->foreign(['orden_id'], 'imagenes_orden_servicio_ibfk_1')->references(['id'])->on('ordenes_servicio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('imagenes_orden_servicio', function (Blueprint $table) {
            $table->dropForeign('imagenes_orden_servicio_ibfk_1');
        });
    }
};
