<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFontsRubroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fonts_rubro', function(Blueprint $table) {

            $table->integer('source_fundings_id')->nullable()->unsigned()->after('rubro_id');
            $table->foreign('source_fundings_id')->references('id')->on('source_fundings');

            $table->integer('tipo_normas_id')->nullable()->unsigned()->after('source_fundings_id');
            $table->foreign('tipo_normas_id')->references('id')->on('tipo_normas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fonts_rubro', function (Blueprint $table) {
            $table->dropColumn('source_fundings_id');
            $table->dropColumn('tipo_normas_id');
        });
    }
}
