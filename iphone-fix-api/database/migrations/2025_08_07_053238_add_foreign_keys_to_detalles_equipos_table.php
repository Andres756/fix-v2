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
        Schema::table('detalles_equipos', function (Blueprint $table) {
            $table->foreign(['inventario_id'], 'detalles_equipos_ibfk_1')->references(['id'])->on('inventarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detalles_equipos', function (Blueprint $table) {
            $table->dropForeign('detalles_equipos_ibfk_1');
        });
    }
};
