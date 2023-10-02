<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImpDelineacionVecinosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imp_delineacion_vecinos', function (Blueprint $table) {
            $table->increments('id');

            $table->text('dirPredVecino');
            $table->text('dirCorrespVecino');

            //RELACION CON EL FOMULARIO
            $table->integer('delineacion_id')->unsigned();
            $table->foreign('delineacion_id')->references('id')->on('imp_delineacion');

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
        Schema::dropIfExists('imp_delineacion_vecinos');
    }
}
