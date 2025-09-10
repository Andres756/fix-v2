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
        Schema::create('plan_separe_log', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('plan_id')->index('plan_id');
            $table->unsignedInteger('tipo_cambio_id')->index('tipo_cambio_id');
            $table->unsignedInteger('inventario_anterior_id')->nullable()->index('inventario_anterior_id');
            $table->unsignedInteger('inventario_nuevo_id')->nullable()->index('inventario_nuevo_id');
            $table->decimal('precio_anterior', 10)->nullable();
            $table->decimal('precio_nuevo', 10)->nullable();
            $table->decimal('monto_devuelto', 10)->nullable();
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('usuario_id')->nullable()->index('usuario_id');
            $table->timestamp('fecha')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan_separe_log');
    }
};
