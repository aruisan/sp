<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateModuleAlmacenV3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('almacen_puc_relaciones', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('puc_debito_code');
            $table->integer('puc_credito_id');
            $table->integer('puc_debito_depreciacion_id');
            $table->integer('puc_credito_depreciacion_id');
            $table->timestamps();
        });

        Schema::table('almacen_articulos', function (Blueprint $table) {
            $table->string('presentacion')->nullable()->after('referencia');
            $table->string('marca')->nullable()->after('referencia');
            $table->integer('vida_util')->nullable()->after('presentacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('almacen_puc_relaciones');

        Schema::table('almacen_articulos', function (Blueprint $table) {
            $table->dropColumn('presentacion');
            $table->dropColumn('marca');
            $table->dropColumn('vida_util');
        });
    }
}
