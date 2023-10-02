<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePacInformeIngresoEgresosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pac_informe_ingreso_egresos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('codigo');
            $table->text('nombre')->nullable();
            $table->string('tipo');
            $table->integer('inicial');
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
        Schema::dropIfExists('pac_informe_ingreso_egresos');
    }
}
