<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBpinVigenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { /*
        Schema::create('bpin_vigencias', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bpin_id')->unsigned();
            $table->integer('vigencia_id')->unsigned();
            $table->integer('rubro_id')->unsigned();
            $table->bigInteger('propios')->nullable();
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
        Schema::dropIfExists('bpin_vigencias');
    }
}
