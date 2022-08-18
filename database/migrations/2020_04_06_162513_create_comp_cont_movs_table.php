<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompContMovsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comp_cont_movs', function (Blueprint $table) {
            $table->increments('id');

            $table->bigInteger('debito');
            $table->bigInteger('credito');

            $table->integer('comp_cont_id')->unsigned();
            $table->foreign('comp_cont_id')->references('id')->on('comp_conts');

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
        Schema::dropIfExists('comp_cont_movs');
    }
}
