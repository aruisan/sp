<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMueblesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('muebles', function (Blueprint $table) {
            $table->increments('id');

            $table->bigInteger('num_factura')->nullable();
            $table->text('descripcion')->nullable();
            $table->date('fecha_ing')->nullable();
            $table->date('fecha_baja')->nullable();
            $table->enum('estado', [0, 1, 2])->nullable();
            $table->integer('cantidad')->nullable();
            $table->integer('avaluo')->nullable();
            $table->integer('depreciacion')->nullable();
            $table->integer('valor_unidad')->nullable();
            $table->integer('nuevo_valor')->nullable();
            $table->integer('vida_util')->nullable();
            $table->enum('tipo', [0, 1]);
            $table->string('ruta')->nullable();

            $table->integer('persona_id')->unsigned()->nullable();
            $table->foreign('persona_id')->references('id')->on('personas');
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
        Schema::dropIfExists('muebles');
    }
}
