<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInformeContableMensualDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('informe_contable_mensual_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('informe_contable_mensual_id');
            $table->integer('puc_alcaldia_id');
            $table->bigInteger('s_credito');
            $table->bigInteger('s_debito');
            $table->bigInteger('m_credito');
            $table->bigInteger('m_debito');
            $table->bigInteger('i_credito');
            $table->bigInteger('i_debito');
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
        Schema::dropIfExists('informe_contable_mensual_datas');
    }
}
