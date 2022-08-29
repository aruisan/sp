<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImpIcaContriTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imp_ica_contri', function (Blueprint $table) {
            $table->increments('id');

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

            $table->text('añoGravable');
            $table->enum('opciondeUso', ['Declaración', 'Pago', 'Corrección']);
            $table->bigInteger('numReferencia')->nullable();
            $table->integer('numEstableLoc');
            $table->integer('numEstableNal');
            //B.
            $table->integer('totIngreOrd')->nullable();
            $table->integer('menosIngreFuera')->nullable();
            $table->integer('totIngreOrdin')->nullable();
            $table->integer('menosIngreDevol')->nullable();
            $table->integer('menosIngreExport')->nullable();
            $table->integer('menosIngreOtrasActiv')->nullable();
            $table->integer('menosIngreActivExcentes')->nullable();
            $table->integer('totIngreGravables')->nullable();
            //C.
            $table->text('codClasiMuni')->nullable();
            $table->integer('tarifa')->nullable();
            $table->integer('impIndyCom')->nullable();
            $table->text('codClasiMuni2')->nullable();
            $table->integer('ingreGravados2')->nullable();
            $table->integer('tarifa2')->nullable();
            $table->integer('impIndyCom2')->nullable();
            $table->text('codClasiMuni3')->nullable();
            $table->integer('ingreGravados3')->nullable();
            $table->integer('tarifa3')->nullable();
            $table->integer('impIndyCom3')->nullable();
            $table->text('codClasiMuni4')->nullable();
            $table->integer('ingreGravados4')->nullable();
            $table->integer('tarifa4')->nullable();
            $table->integer('impIndyCom4')->nullable();
            $table->text('codClasiMuni5')->nullable();
            $table->integer('ingreGravados5')->nullable();
            $table->integer('totIngreGravado')->nullable();
            $table->integer('totImpuesto')->nullable();
            $table->integer('genEnergiaCapacidad')->nullable();
            $table->integer('impLey56')->nullable();
            //D
            $table->integer('totImpIndyCom')->nullable();
            $table->integer('impAviyTableros')->nullable();
            $table->integer('pagoUndComer')->nullable();
            $table->integer('sobretasaBomberil')->nullable();
            $table->integer('sobretasaSeguridad')->nullable();
            $table->integer('totImpCargo')->nullable();
            $table->integer('menosValorExencion')->nullable();
            $table->integer('menosRetenciones')->nullable();
            $table->integer('menosAutorretenciones')->nullable();
            $table->integer('menosAnticipoLiquidado')->nullable();
            $table->integer('anticipoAñoSiguiente')->nullable();
            $table->enum('SANCIONES', ['EXTEMPORANEIDAD', 'CORRECCIÓN', 'INEXACTITUD','OTRA','NINGUNA']);
            $table->text('cualOtra')->nullable();
            $table->integer('sancionesVal')->nullable();
            $table->integer('menosSaldoaFavorPredio')->nullable();
            $table->integer('totSaldoaCargo')->nullable();
            $table->integer('totSaldoaFavor')->nullable();
            //D.
            $table->integer('valoraPagar')->nullable();
            $table->integer('valorDesc')->nullable();
            $table->integer('interesesMora')->nullable();
            $table->integer('totPagar')->nullable();
            //VII. FIRMAS Y FECHA DE RECEPCIÓN
            $table->date('presentacion');

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
        Schema::dropIfExists('imp_ica');
    }
}
