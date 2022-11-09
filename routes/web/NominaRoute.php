<?php
Route::group([ 'middleware' => 'auth', 'prefix' => 'nomina'] ,function(){
    Route::get('/empleados', "Nomina\EmpleadoController@index")->name('nomina.empleados.index');
    Route::get('/empleados/create', function (){
        return view('nomina.empleados.create');
    })->name('nomina.empleados.create');
    Route::post('/empleados/create', "Nomina\EmpleadoController@create")->name('nomina.empleados.create');
    Route::get('/empleados/edit/{id}', "Nomina\EmpleadoController@edit")->name('nomina.empleados.edit');
    Route::post('/empleados/edit/{id}', "Nomina\EmpleadoController@update")->name('nomina.empleados.edit');
    Route::get('/pagos', "Nomina\PagoController@index")->name('nomina.pagos.index');
});
