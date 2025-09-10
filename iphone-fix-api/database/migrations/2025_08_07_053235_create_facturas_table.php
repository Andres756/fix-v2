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
        Schema::create('facturas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cliente_id')->index('cliente_id');
            $table->unsignedBigInteger('usuario_id')->index('usuario_id');
            $table->unsignedInteger('tipo_venta_id')->index('tipo_venta_id');
            $table->unsignedInteger('estado_id')->index('estado_id');
            $table->decimal('total', 10);
            $table->unsignedInteger('forma_pago_id')->nullable()->index('forma_pago_id');
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
        Schema::dropIfExists('facturas');
    }
};
