<?php
use App\Helpers\FechaHelper;

Route::group([ 'middleware' => 'auth'] ,function(){
   // Route::get('chequeo-cuenta/{id}', 'tramitecuenta\TramiteCuentaController@chequear')->name('chequeo-cuenta.form');
    Route::get('/users/{usuario}/impersonate/start', 'PersonalizarController@start')->name('personalizar.start');
    Route::get('/users/impersonate/stop', 'PersonalizarController@stop')->name('personalizar.stop');
    Route::get('/users/impersonate/seleccionar', 'PersonalizarController@users')->name('personalizar.seleccionar');
    Route::get('/pruebas',  function(){
        $data  = FechaHelper::estructura_periodos();
        dd($data);
    });


});