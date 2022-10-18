<?php
Route::group([ 'middleware' => 'auth', 'prefix' => 'nomina'] ,function(){
    Route::get('/empleados', "Nomina\EmpleadoController@index")->name('nomina.empleados.index');
    Route::get('/empleados/create', function (){
        return view('nomina.empleados.create');
    })->name('nomina.empleados.create');
    Route::post('/empleados/create', "Nomina\EmpleadoController@create")->name('nomina.empleados.create');
    Route::get('/pagos', "Nomina\PagoController@index")->name('nomina.pagos.index');
});
