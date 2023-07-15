<?php

Route::group([ 'middleware' => 'auth', 'prefix' => 'administrativo/contabilidad'] ,function(){
    Route::get('/pac-informe/{tipo}', "\App\Http\Controllers\Administrativo\Tesoreria\PacController@informe_temporal")->name('pac.informe');
    Route::get('/blance-inicial/{mes}','Administrativo\Contabilidad\Balances\InicialController@index')->name('balance.inicial');

    Route::get('/blance-pre-prueba/{mes}','Administrativo\Contabilidad\Balances\PruebaController@pre_informe')->name('balance.pre-prueba');
    Route::get('/blance-prueba/{informe}/{puc}','Administrativo\Contabilidad\Balances\PruebaController@generar_informe')->name('balance.prueba');
    Route::get('/blance-prueba-relaciones/{informe}','Administrativo\Contabilidad\Balances\PruebaController@generar_informe_relaciones')->name('balance.prueba-relaciones');
    Route::get('/blance-prueba-informe/{informe}','Administrativo\Contabilidad\Balances\PruebaController@informe')->name('balance.prueba-informe');
    Route::get('/blance-prueba-reload-informe/{informe}','Administrativo\Contabilidad\Balances\PruebaController@reload_informe')->name('balance.prueba-informe-reload');

    Route::get('/chip-contable/{age}/{trimestre}','Administrativo\Contabilidad\Balances\ChipController@informe_contable')->name('chip.contable');
    Route::get('/chip-contable-actualizacion/{age}/{trimestre}','Administrativo\Contabilidad\Balances\ChipController@informe_contable_actualizacion')->name('chip.contable.actualizacion');

    Route::get('/chip-contable/{puc}','Administrativo\Contabilidad\Balances\ChipController@informe_contable_ajax');
    Route::get('/chip-contable-ver/{puc}','Administrativo\Contabilidad\Balances\ChipController@informe_contable_puc_ver')->name('chip.contable.puc.ver');
    Route::get('/chip-contable-ver','Administrativo\Contabilidad\Balances\ChipController@informe_contable_pucs')->name('chip.contable.pucs');
});