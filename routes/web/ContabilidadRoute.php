<?php

Route::group([ 'middleware' => 'auth', 'prefix' => 'administrativo/contabilidad'] ,function(){
    Route::get('/pac-informe/{tipo}', "\App\Http\Controllers\Administrativo\Tesoreria\PacController@informe_temporal")->name('pac.informe');
    Route::get('/blance-inicial','Administrativo\Contabilidad\Balances\InicialController@index')->name('balance.inicial');
    Route::get('/blance-prueba','Administrativo\Contabilidad\Balances\PruebaController@informe')->name('balance.prueba');

    Route::get('/chip-contable','Administrativo\Contabilidad\Balances\ChipController@informe_contable')->name('chip.contable');
});