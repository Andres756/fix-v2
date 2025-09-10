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
        Schema::create('inventarios', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
            $table->string('nombre_detallado')->nullable();
            $table->string('codigo', 100)->nullable();
            $table->unsignedInteger('categoria_id')->nullable()->index('categoria_id');
            $table->unsignedInteger('lote_id')->nullable()->index('lote_id');
            $table->unsignedInteger('estado_inventario_id')->nullable()->index('estado_inventario_id');
            $table->unsignedInteger('proveedor_id')->nullable()->index('fk_proveedor');
            $table->unsignedInteger('tipo_inventario_id')->index('tipo_inventario_id');
            $table->integer('stock')->nullable()->default(0);
            $table->integer('stock_minimo')->nullable()->default(0);
            $table->decimal('precio', 10);
            $table->decimal('costo', 10);
            $table->decimal('costo_mayor', 10)->nullable()->default(0);
            $table->enum('tipo_impuesto', ['n/a', 'porcentaje', 'fijo'])->nullable()->default('n/a');
            $table->decimal('valor_impuesto', 10)->nullable()->default(0);
            $table->decimal('precio_final', 10)->nullable()->storedAs('case when `tipo_impuesto` = \'porcentaje\' then `precio` + `precio` * `valor_impuesto` / 100 when `tipo_impuesto` = \'fijo\' then `precio` + `valor_impuesto` else `precio` end');
            $table->boolean('activo')->nullable()->default(true);
            $table->date('fecha_ingreso')->nullable();
            $table->text('notas')->nullable();
            $table->string('ruta_imagen')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventarios');
    }
};
