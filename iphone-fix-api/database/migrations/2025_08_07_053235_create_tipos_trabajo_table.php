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
        Schema::create('tipos_trabajo', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 100)->unique('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('costo_cliente', 10);
            $table->enum('tipo_pago_tecnico', ['porcentaje', 'valor_fijo'])->default('valor_fijo');
            $table->decimal('valor_pago_tecnico', 10);
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
        Schema::dropIfExists('tipos_trabajo');
    }
};
