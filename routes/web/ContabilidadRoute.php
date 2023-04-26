<?php

Route::group([ 'middleware' => 'auth', 'prefix' => 'administrativo/contabilidad'] ,function(){
    Route::get('/pac-informe/{tipo}', "\App\Http\Controllers\Administrativo\Tesoreria\PacController@informe_temporal")->name('pac.informe');
    Route::get('/blance-inicial','Administrativo\Contabilidad\Balances\InicialController@index')->name('balance.inicial');
    Route::get('/blance-prueba','Administrativo\Contabilidad\Balances\PruebaController@informe')->name('balance.prueba');

    Route::get('/chip-contable','Administrativo\Contabilidad\Balances\ChipController@informe_contable')->name('chip.contable');
    Route::get('/chip-contable-actualizacion','Administrativo\Contabilidad\Balances\ChipController@informe_contable_actualizacion')->name('chip.contable.actualizacion');
    Route::get('/chip-contable/{puc}','Administrativo\Contabilidad\Balances\ChipController@informe_contable_ajax');
    Route::get('/chip-contable-ver/{puc}','Administrativo\Contabilidad\Balances\ChipController@informe_contable_puc_ver')->name('chip.contable.puc.ver');
    Route::get('/chip-contable-ver','Administrativo\Contabilidad\Balances\ChipController@informe_contable_pucs')->name('chip.contable.pucs');
});