<?php

Route::group([ 'middleware' => 'auth', 'prefix' => 'import'] ,function(){
    Route::get('/estadistica', "ImportEstadisticaPresupuestoController@create");
    Route::post('/estadistica', "ImportEstadisticaPresupuestoController@import")->name('import.presupuesto-estadistica');

    Route::get('/terceros', "ImportEstadisticaPresupuestoController@create_terceros");
    Route::post('/terceros', "ImportEstadisticaPresupuestoController@import_terceros")->name('import.tercero-estadistica');

    Route::get('/empleados', "ImportEstadisticaPresupuestoController@create_empleados");
    Route::post('/empleados', "ImportEstadisticaPresupuestoController@import_empleados")->name('import.empleado');
});