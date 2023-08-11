<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCosoMedicinasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coso_medicinas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('medicamento')->nullable();
            $table->string('dosis_diaria')->nullable();
            $table->time('hora')->nullable();
            $table->string('termino')->nullable();
            $table->enum('aplica', ['Si', 'No']);
            $table->integer('coso_veterinario_id');
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
        Schema::dropIfExists('coso_medicinas');
    }
}
