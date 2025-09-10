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
        Schema::table('detalle_trabajo', function (Blueprint $table) {
            $table->foreign(['orden_id'], 'detalle_trabajo_ibfk_1')->references(['id'])->on('ordenes_servicio');
            $table->foreign(['tipo_trabajo_id'], 'detalle_trabajo_ibfk_2')->references(['id'])->on('tipos_trabajo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detalle_trabajo', function (Blueprint $table) {
            $table->dropForeign('detalle_trabajo_ibfk_1');
            $table->dropForeign('detalle_trabajo_ibfk_2');
        });
    }
};
