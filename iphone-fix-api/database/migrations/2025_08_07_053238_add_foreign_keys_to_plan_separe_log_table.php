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
        Schema::table('plan_separe_log', function (Blueprint $table) {
            $table->foreign(['plan_id'], 'plan_separe_log_ibfk_1')->references(['id'])->on('plan_separe');
            $table->foreign(['usuario_id'], 'plan_separe_log_ibfk_3')->references(['id'])->on('users');
            $table->foreign(['inventario_nuevo_id'], 'plan_separe_log_ibfk_5')->references(['id'])->on('inventarios');
            $table->foreign(['tipo_cambio_id'], 'plan_separe_log_ibfk_2')->references(['id'])->on('tipos_cambio_plan_separe');
            $table->foreign(['inventario_anterior_id'], 'plan_separe_log_ibfk_4')->references(['id'])->on('inventarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_separe_log', function (Blueprint $table) {
            $table->dropForeign('plan_separe_log_ibfk_1');
            $table->dropForeign('plan_separe_log_ibfk_3');
            $table->dropForeign('plan_separe_log_ibfk_5');
            $table->dropForeign('plan_separe_log_ibfk_2');
            $table->dropForeign('plan_separe_log_ibfk_4');
        });
    }
};
