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
        Schema::create('abonos_plan_separe', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('plan_id')->index('plan_id');
            $table->decimal('monto', 10);
            $table->unsignedInteger('forma_pago_id')->nullable()->index('forma_pago_id');
            $table->unsignedBigInteger('usuario_id')->nullable()->index('usuario_id');
            $table->date('fecha_abono')->nullable()->default('CURRENT_DATE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('abonos_plan_separe');
    }
};
