<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImpIcaRetenedorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imp_ica_retenedor', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('añoGravable');
            $table->integer('periodo');
            $table->enum('opciondeUso', ['Declaración', 'Pago', 'Corrección']);
            $table->enum('codAgente', ['1', '2', '3', '4', '5','6']);

            $table->bigInteger('contratosObra');
            $table->bigInteger('contratosPrestServ');
            $table->bigInteger('compraBienes');
            $table->bigInteger('otrasActiv');
            $table->bigInteger('practicadasPeriodosAnt');
            $table->bigInteger('totRetenciones');
            $table->bigInteger('devolucionExceso');
            $table->bigInteger('devolucionRetencion');
            $table->bigInteger('totalRetencion');
            $table->bigInteger('sancionExtemp');
            $table->bigInteger('sancionCorreccion');
            $table->bigInteger('interesMoratorio');
            $table->bigInteger('pagoTotal');
            $table->bigInteger('idSignatario');
            $table->text('nameSignatario');
            $table->enum('signatario', ['repLegal', 'delegado', 'principal']);
            $table->bigInteger('tpRevFisc')->nullable();
            $table->text('nameRevFisc')->nullable();

            $table->date('presentacion');

            //RELACION CON EL USUARIO
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            //RELACION CON EL FUNCIONARIO QUE VALIDO INSCRIPCIÓN
            $table->integer('user_inscripcion_id')->unsigned()->nullable();
            $table->foreign('user_inscripcion_id')->references('id')->on('users');
            $table->date('fechaInscripUser')->nullable();
            //RELACION CON EL FUNCIONARIO QUE VALIDO ACTUALIZACION
            $table->integer('user_update_id')->unsigned()->nullable();
            $table->foreign('user_update_id')->references('id')->on('users');
            $table->date('fechaUpdateUser')->nullable();

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
        Schema::dropIfExists('imp_ica_retenedor');
    }
}
