<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventariosTable extends Migration
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

            $table->bigInteger('num_factura')->nullable();
            $table->text('descripcion')->nullable();
            $table->text('unidad')->nullable();
            $table->integer('valor_unidad')->nullable();
            $table->integer('valor_final')->nullable();
            $table->integer('cantidad')->nullable();
            $table->date('fecha_ing')->nullable();
            $table->date('fecha_salida')->nullable();
            $table->enum('tipo', [0, 1]);
            $table->string('ruta')->nullable();

            $table->integer('producto_id')->unsigned();
            $table->foreign('producto_id')->references('id')->on('productos');

            $table->timestamps();
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
}
