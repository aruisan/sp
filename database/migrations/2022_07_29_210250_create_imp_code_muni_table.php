<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImpCodeMuniTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imp_code_muni', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('code_ciudad');
            $table->text('name_ciudad');
            $table->integer('code_dept');
            $table->text('name_dept');

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
        Schema::dropIfExists('imp_code_muni');
    }
}
