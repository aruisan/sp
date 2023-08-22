<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChipContabilidadValorInicialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chip_contabilidad_valor_inicials', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('puc_id');
            $table->integer('trimestre');
            $table->integer('age');
            $table->bigInteger('valor');   
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
        Schema::dropIfExists('chip_contabilidad_valor_inicials');
    }
}
