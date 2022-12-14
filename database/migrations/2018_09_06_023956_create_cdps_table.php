<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCdpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cdps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('code');
            $table->text('name');
            $table->integer('valueControl')->nullable();
            $table->integer('valor');
            $table->date('fecha');

            $table->integer('dependencia_id')->unsigned();
            $table->foreign('dependencia_id')->references('id')->on('dependencias');

            $table->enum('secretaria_e', [0, 1, 2, 3]);
            $table->date('ff_secretaria_e');
            $table->enum('alcalde_e', [0, 1, 2, 3])->nullable();
            $table->date('ff_alcalde_e')->nullable();
            $table->text('motivoAlcalde')->nullable();
            $table->enum('jefe_e', [0, 1, 2, 3])->nullable();
            $table->date('ff_jefe_e')->nullable();
            $table->text('motivo')->nullable();
            $table->text('observacion')->nullable();
            $table->integer('saldo');

            $table->string('ruta')->nullable();

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
        Schema::dropIfExists('cdps');
    }
}
