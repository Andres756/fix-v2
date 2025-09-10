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
        Schema::table('abonos_plan_separe', function (Blueprint $table) {
            $table->foreign(['plan_id'], 'abonos_plan_separe_ibfk_1')->references(['id'])->on('plan_separe');
            $table->foreign(['usuario_id'], 'abonos_plan_separe_ibfk_3')->references(['id'])->on('users');
            $table->foreign(['forma_pago_id'], 'abonos_plan_separe_ibfk_2')->references(['id'])->on('formas_pago');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('abonos_plan_separe', function (Blueprint $table) {
            $table->dropForeign('abonos_plan_separe_ibfk_1');
            $table->dropForeign('abonos_plan_separe_ibfk_3');
            $table->dropForeign('abonos_plan_separe_ibfk_2');
        });
    }
};
