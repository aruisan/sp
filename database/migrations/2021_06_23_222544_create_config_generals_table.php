<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigGeneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_generals', function (Blueprint $table) {
            $table->increments('id');

            $table->string('nombres', 200);
            $table->enum('tipo', ['PRESIDENTE', 'SECRETARIA GENERAL','PRIMER VICEPRESIDENTE','SEGUNDO VICEPRESIDENTE','CONTADOR']);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');

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
        Schema::dropIfExists('config_generals');
    }
}
