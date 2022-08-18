<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCiRubrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('CI_rubros', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('valor')->nullable();
            $table->integer('comprobante_ingreso_id')->unsigned();
            $table->foreign('comprobante_ingreso_id')->references('id')->on('comprobante_ingresos');
            $table->integer('rubro_id')->unsigned();
            $table->foreign('rubro_id')->references('id')->on('rubros');
            $table->integer('fonts_rubro_id')->nullable()->unsigned();
            $table->foreign('fonts_rubro_id')->references('id')->on('fonts_rubro');

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
        Schema::dropIfExists('CI_rubros');
    }
}
