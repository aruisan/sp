<?php
Route::group([ 'middleware' => 'auth', 'prefix' => 'nomina'] ,function(){
    Route::get('/', "Nomina\NominaController@index")->name('nomina.index');
    Route::get('/create', "Nomina\NominaController@create")->name('nomina.create');
    Route::post('/', "Nomina\NominaController@store")->name('nomina.store');

    //empleqados
    Route::get('/empleados', "Nomina\EmpleadoController@index")->name('nomina.empleados.index');
    Route::get('/empleados/create', "Nomina\EmpleadoController@create")->name('nomina.empleados.create');
    Route::post('/empleados/create', "Nomina\EmpleadoController@store")->name('nomina.empleados.create');
    Route::get('/empleados/edit/{employee}', "Nomina\EmpleadoController@edit")->name('nomina.empleados.edit');
    Route::post('/empleados/edit/{employee}', "Nomina\EmpleadoController@update")->name('nomina.empleados.edit');
    Route::get('/pagos', "Nomina\PagoController@index")->name('nomina.pagos.index');
});
