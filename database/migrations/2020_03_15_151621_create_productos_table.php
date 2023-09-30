<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->increments('id');

            $table->text('nombre');
            $table->integer('cant_inicial')->default('0');
            $table->integer('cant_actual')->default('0');
            $table->integer('cant_minima');
            $table->integer('cant_maxima');
            $table->enum('metodo', [0, 1]);
            $table->enum('tipo', [0, 1]);
            $table->integer('valor_inicial');
            $table->integer('valor_actual');

            $table->integer('rubros_puc_id')->unsigned();
            $table->foreign('rubros_puc_id')->references('id')->on('rubros_pucs');

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
        Schema::dropIfExists('productos');
    }
}
