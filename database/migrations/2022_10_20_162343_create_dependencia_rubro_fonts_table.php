<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDependenciaRubroFontsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        Schema::create('dependencia_rubro_fonts', function (Blueprint $table) {
            $table->increments('id');

            //RELACION CON LAS DEPENDENCIAS
            $table->integer('dependencia_id')->unsigned();
            $table->foreign('dependencia_id')->references('id')->on('dependencias');

            //RELACION CON EL RUBRO
            $table->integer('rubro_font_id')->unsigned();
            $table->foreign('rubro_font_id')->references('id')->on('fonts_rubro');

            //RELACION CON LA VIGENCIA
            $table->integer('vigencia_id')->unsigned();
            $table->foreign('vigencia_id')->references('id')->on('vigencias');

            $table->bigInteger('value')->nullable();
            $table->bigInteger('saldo')->nullable();

            $table->timestamps();
        });
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dependencia_rubro_fonts');
    }
}
