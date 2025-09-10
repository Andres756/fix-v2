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
        Schema::table('plan_separe', function (Blueprint $table) {
            $table->foreign(['cliente_id'], 'plan_separe_ibfk_1')->references(['id'])->on('clientes');
            $table->foreign(['inventario_id_asignado'], 'plan_separe_ibfk_3')->references(['id'])->on('inventarios');
            $table->foreign(['inventario_id'], 'plan_separe_ibfk_2')->references(['id'])->on('inventarios');
            $table->foreign(['estado_id'], 'plan_separe_ibfk_4')->references(['id'])->on('estados_plan_separe');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_separe', function (Blueprint $table) {
            $table->dropForeign('plan_separe_ibfk_1');
            $table->dropForeign('plan_separe_ibfk_3');
            $table->dropForeign('plan_separe_ibfk_2');
            $table->dropForeign('plan_separe_ibfk_4');
        });
    }
};
