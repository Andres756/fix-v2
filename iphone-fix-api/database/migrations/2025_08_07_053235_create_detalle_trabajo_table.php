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
        Schema::create('detalle_trabajo', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('orden_id')->index('orden_id');
            $table->unsignedInteger('tipo_trabajo_id')->index('tipo_trabajo_id');
            $table->decimal('costo_aplicado', 10)->nullable();
            $table->decimal('pago_tecnico', 10)->nullable();
            $table->text('observaciones')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalle_trabajo');
    }
};
