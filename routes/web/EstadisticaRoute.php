<?php


Route::group([ 'middleware' => 'auth', 'prefix' => 'estadistica'] ,function(){
    Route::get('/', 'Estadistica\EstadisticaController@index')->name('estadistica.index');
    Route::get('/reserva-vuelo', 'Estadistica\ReservaVueloController@index')->name('reservaVuelo.index');
    Route::post('/reserva-vuelo', 'Estadistica\ReservaVueloController@store')->name('reservaVuelo.store');
});