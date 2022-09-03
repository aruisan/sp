<?php


Route::group([ 'middleware' => 'auth', 'prefix' => 'estadistica'] ,function(){
    Route::get('/', 'Estadistica\EstadisticaController@index')->name('estadistica.index');
    Route::get('/reserva-vuelo', 'Estadistica\ReservaVueloController@index')->name('reservaVuelo.index');
    Route::post('/reserva-vuelo', 'Estadistica\ReservaVueloController@store')->name('reservaVuelo.store');
    Route::get('/barco', 'Estadistica\BarcoController@index')->name('barco.index');
    ROute::post('/barco', 'Estadistica\BarcoController@store')->name('barco.store');
});