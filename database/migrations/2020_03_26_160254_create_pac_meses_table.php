<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePacMesesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pac_meses', function (Blueprint $table) {
            $table->increments('id');

            $table->string('mes');
            $table->bigInteger('valor');

            $table->integer('pac_id')->unsigned();
            $table->foreign('pac_id')->references('id')->on('pacs');
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
        Schema::dropIfExists('pac_meses');
    }
}
