<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBancosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bancos', function (Blueprint $table) {
            $table->increments('id');

            $table->string('numero_cuenta');
            $table->text('descripcion');
            $table->integer('valor_inicial');
            $table->integer('valor_actual');
            $table->enum('estado', [0, 1]);

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
        Schema::dropIfExists('bancos');
    }
}
