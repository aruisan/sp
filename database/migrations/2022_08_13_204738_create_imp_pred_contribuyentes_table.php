<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImpPredContribuyentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imp_pred_contribuyentes', function (Blueprint $table) {
            $table->increments('id');

            $table->char('numCatastral', 15);
            $table->text('numIdent');
            $table->text('contribuyente');
            $table->text('areaTerreno')->nullable();
            $table->text('dir_predio')->nullable();
            $table->text('dir_notificacion')->nullable();
            $table->text('municipio')->nullable();
            $table->text('email')->nullable();
            $table->text('whatsapp')->nullable();
            $table->text('facebook')->nullable();
            $table->text('otra_red')->nullable();
            $table->bigInteger('valor_deuda')->default(0);
            $table->integer('años_deuda')->default(0);

            $table->softDeletes();
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
        Schema::dropIfExists('imp_pred_contribuyentes');
    }
}
