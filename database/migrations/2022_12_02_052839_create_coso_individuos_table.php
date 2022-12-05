<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCosoIndividuosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coso_individuos', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('date_at')->nullable();
            $table->string('ficha_ingreso')->nullable();
            $table->string('nombre')->nullable()->default('text');
            $table->string('tipo')->nullable();
            $table->string('peso')->nullable();
            $table->string('talla')->nulable();
            $table->enum('sexo', ['Masculino', 'Femenino'])->nullable();
            $table->string('color')->nullable();
            $table->text('Observacion')->nullable();
            $table->string('marcas')->nullable();
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
        Schema::dropIfExists('coso_individuos');
    }
}
