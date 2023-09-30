<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImpRitActivTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imp_rit_activ', function (Blueprint $table) {
            $table->increments('id');

            //RELACION CON EL RIT
            $table->integer('rit_id')->unsigned();
            $table->foreign('rit_id')->references('id')->on('imp_rit');

            $table->text('codCIIU')->nullable();

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
        Schema::dropIfExists('imp_rit_activ');
    }
}
