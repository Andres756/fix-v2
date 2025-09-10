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
        Schema::table('descuentos', function (Blueprint $table) {
            $table->foreign(['tipo_descuento_id'], 'descuentos_ibfk_1')->references(['id'])->on('tipos_descuento');
            $table->foreign(['aplica_a_id'], 'descuentos_ibfk_2')->references(['id'])->on('aplicacion_descuento');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('descuentos', function (Blueprint $table) {
            $table->dropForeign('descuentos_ibfk_1');
            $table->dropForeign('descuentos_ibfk_2');
        });
    }
};
