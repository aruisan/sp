<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCosoVeterinariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coso_veterinarios', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre_veterinario')->nullable();
            $table->string('tarjeta_profesional')->nullable();
            $table->string('cedula')->nullable();
            $table->string('celular')->nullable();
            $table->integer('coso_individuo_id');
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
        Schema::dropIfExists('coso_veterinarios');
    }
}
