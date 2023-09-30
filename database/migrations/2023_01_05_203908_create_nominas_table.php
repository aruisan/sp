<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNominasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nominas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('salud');
            $table->string('pension');
            $table->string('riesgos');
            $table->string('sena');
            $table->string('icbf');
            $table->string('caja_compensacion');
            $table->string('cesantias');
            $table->string('interes_cesantias');
            $table->string('prima_navidad');
            $table->string('vacaciones');
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
        Schema::dropIfExists('nominas');
    }
}
