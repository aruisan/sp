<?php

Route::group([ 'middleware' => 'auth', 'prefix' => 'administrativo/contabilidad'] ,function(){
    
    Route::get('/pac-informe/{tipo}', "\App\Http\Controllers\Administrativo\Tesoreria\PacController@informe_temporal")->name('pac.informe');
    Route::get('/blance-inicial/{mes}','Administrativo\Contabilidad\Balances\InicialController@index')->name('balance.inicial');

    Route::get('/blance-prueba-trim/{mes_1}/{mes_2}','Administrativo\Contabilidad\Balances\PruebaController@balanceTrim')->name('balance.prueba.trim');
    Route::get('/blance-pre-prueba/{mes}','Administrativo\Contabilidad\Balances\PruebaController@pre_informe')->name('balance.pre-prueba');
    Route::get('/blance-prueba/{informe}/{puc}','Administrativo\Contabilidad\Balances\PruebaController@generar_informe')->name('balance.prueba');
    Route::get('/blance-prueba-relaciones/{informe}','Administrativo\Contabilidad\Balances\PruebaController@generar_informe_relaciones')->name('balance.prueba-relaciones');
    Route::get('/blance-prueba-informe/{informe}','Administrativo\Contabilidad\Balances\PruebaController@informe')->name('balance.prueba-informe');
    Route::get('/blance-prueba-nivel/{nivel}/{informe}','Administrativo\Contabilidad\Balances\PruebaController@informe_nivel')->name('balance.prueba-nivel');
    Route::get('/blance-prueba-reload-informe/{informe}','Administrativo\Contabilidad\Balances\PruebaController@reload_informe')->name('balance.prueba-informe-reload');
    Route::get('/puc-data/{puc}','Administrativo\Contabilidad\Balances\PruebaController@puc_data')->name('balance.puc_data');

    Route::get('/blance-general-pdf/{age}/{mes}/{tipo}','Administrativo\Contabilidad\Balances\GeneralController@pdf')->name('balance-general.pdf');

    Route::get('/chip-pre/{age}/{trimestre}','Administrativo\Contabilidad\Balances\ChipController@pre_informe')->name('chip.pre');
    Route::get('/chip/{informe}/{puc}','Administrativo\Contabilidad\Balances\ChipController@generar_informe')->name('chip.generar');
    Route::get('/chip-relaciones/{informe}','Administrativo\Contabilidad\Balances\ChipController@generar_informe_relaciones')->name('chip.relaciones');
    Route::get('/chip-informe/{informe}','Administrativo\Contabilidad\Balances\ChipController@informe')->name('chip-informe');
    Route::get('/chip-reload-informe/{informe}','Administrativo\Contabilidad\Balances\ChipController@reload_informe')->name('chip.reload');
/*
    Route::get('/chip-contable/{age}/{trimestre}','Administrativo\Contabilidad\Balances\ChipController@informe_contable')->name('');
    Route::get('/chip-contable-actualizacion/{age}/{trimestre}','Administrativo\Contabilidad\Balances\ChipController@informe_contable_actualizacion')->name('chip.contable.actualizacion');
*/
    Route::get('/chip-contable/{puc}','Administrativo\Contabilidad\Balances\ChipController@informe_contable_ajax');
    Route::get('/chip-contable-ver/{puc}','Administrativo\Contabilidad\Balances\ChipController@informe_contable_puc_ver')->name('chip.contable.puc.ver');
    Route::get('/chip-contable-ver','Administrativo\Contabilidad\Balances\ChipController@informe_contable_pucs')->name('chip.contable.pucs');


    Route::get('/estado-resultado/{age}/{mes}/{tipo}','Administrativo\Contabilidad\EstadoResultadoController@vista')->name('estado-resultado');
});