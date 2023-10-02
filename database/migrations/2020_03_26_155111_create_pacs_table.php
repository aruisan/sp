<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePacsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pacs', function (Blueprint $table) {
            $table->increments('id');

            $table->bigInteger('situacion_fondos');
            $table->bigInteger('aprobado');
            $table->bigInteger('rezago');
            $table->bigInteger('distribuir');
            $table->bigInteger('total_distri');

            $table->integer('rubro_id')->unsigned();
            $table->foreign('rubro_id')->references('id')->on('rubros');

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
        Schema::dropIfExists('pacs');
    }
}
