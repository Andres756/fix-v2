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
        Schema::create('plan_separe', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cliente_id')->index('cliente_id');
            $table->unsignedInteger('inventario_id')->index('inventario_id');
            $table->unsignedInteger('inventario_id_asignado')->nullable()->index('inventario_id_asignado');
            $table->boolean('cambio_equipo')->nullable()->default(false);
            $table->decimal('precio_total', 10);
            $table->decimal('porcentaje_minimo', 5)->nullable()->default(90);
            $table->decimal('abono_inicial', 10)->nullable()->default(0);
            $table->decimal('monto_devuelto', 10)->nullable()->default(0);
            $table->unsignedInteger('estado_id')->index('estado_id');
            $table->text('observaciones')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan_separe');
    }
};
