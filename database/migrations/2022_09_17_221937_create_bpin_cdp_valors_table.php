<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBpinCdpValorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bpin_cdp_valors', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('valor');
            $table->bigInteger('valor_disp')->nullable();
            $table->integer('cdp_id')->unsigned();
            $table->foreign('cdp_id')->references('id')->on('cdps');
            $table->string('cod_actividad','200');

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
        Schema::dropIfExists('bpin_cdp_valors');
    }
}
