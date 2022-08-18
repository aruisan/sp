<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompContsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comp_conts', function (Blueprint $table) {
            $table->increments('id');

            $table->date('fecha');
            $table->bigInteger('code');
            $table->text('descripcion');

            $table->integer('tipo_comp_id')->unsigned();
            $table->foreign('tipo_comp_id')->references('id')->on('tipo_comps');

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
        Schema::dropIfExists('comp_conts');
    }
}
