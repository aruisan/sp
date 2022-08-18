<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRubrosTableCuipos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rubros', function(Blueprint $table){
            //SE CREAN LAS RELACIONES A LAS NUEVAS TABLAS DEL CUIPO

            $table->integer('plantilla_cuipos_id')->nullable()->unsigned()->after('code_contractuales_id');
            $table->foreign('plantilla_cuipos_id')->references('id')->on('plantilla_cuipos');

            $table->integer('terceros_id')->nullable()->unsigned()->after('plantilla_cuipos_id');
            $table->foreign('terceros_id')->references('id')->on('terceros');

            $table->integer('public_politics_id')->nullable()->unsigned()->after('terceros_id');
            $table->foreign('public_politics_id')->references('id')->on('public_politics');

            $table->integer('budget_sections_id')->nullable()->unsigned()->after('public_politics_id');
            $table->foreign('budget_sections_id')->references('id')->on('budget_sections');

            $table->integer('vigencia_gastos_id')->nullable()->unsigned()->after('budget_sections_id');
            $table->foreign('vigencia_gastos_id')->references('id')->on('vigencia_gastos');

            $table->integer('sectors_id')->nullable()->unsigned()->after('vigencia_gastos_id');
            $table->foreign('sectors_id')->references('id')->on('sectors');

            $table->integer('fund_situations_id')->nullable()->unsigned()->after('sectors_id');
            $table->foreign('fund_situations_id')->references('id')->on('fund_situations');

            $table->integer('additional_budget_sections_id')->nullable()->unsigned()->after('fund_situations_id');
            $table->foreign('additional_budget_sections_id')->references('id')->on('additional_budget_sections');

            $table->integer('sector_details_id')->nullable()->unsigned()->after('additional_budget_sections_id');
            $table->foreign('sector_details_id')->references('id')->on('sector_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rubros', function (Blueprint $table) {
            $table->dropColumn('plantilla_cuipos_id');
            $table->dropColumn('terceros_id');
            $table->dropColumn('public_politics_id');
            $table->dropColumn('budget_sections_id');
            $table->dropColumn('vigencia_gastos_id');
            $table->dropColumn('sectors_id');
            $table->dropColumn('fund_situations_id');
            $table->dropColumn('additional_budget_sections_id');
            $table->dropColumn('sector_details_id');
        });
    }
}
