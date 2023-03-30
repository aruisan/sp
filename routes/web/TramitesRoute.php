<?php

Route::group([ 'middleware' => 'auth'] ,function(){
    Route::resource('tramites-cuentas', 'tramitecuenta\TramiteCuentaController');
    Route::get('chequeo-cuenta/{id}', 'tramitecuenta\TramiteCuentaController@chequear')->name('chequeo-cuenta.form');
    Route::post('chequeo-cuenta', 'tramitecuenta\TramiteCuentaController@chequearUpdate')->name('chequeo-cuenta.store');
    Route::get('tramites-cuentas/{id}/pdf', 'tramitecuenta\TramiteCuentaController@pdf')->name('tramites-cuentas.pdf');
    Route::get('tramites-cuentas/{id}/logs', 'tramitecuenta\TramiteCuentaController@logs')->name('tramites-cuentas.logs');

    Route::get('tramites-cuentas/{id}/recibido', 'tramitecuenta\TramiteCuentaController@updateRecibido')->name('aprobador_cuentas.recibido');
    Route::get('tramites-cuentas/Aprobar/{id}', 'tramitecuenta\TramiteCuentaController@aprobar')->name('aprobador_cuentas.aprobar');
    Route::post('tramites-cuentas/estado-devolver', 'tramitecuenta\TramiteCuentaController@devolver')->name('aprobador_cuentas.estado-devolver');
    Route::post('tramites-cuentas/estado-aplazar', 'tramitecuenta\TramiteCuentaController@aplazar')->name('aprobador_cuentas.estado-aplazar');
});