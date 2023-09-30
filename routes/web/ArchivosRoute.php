<?php

Route::group([ 'middleware' => 'auth', 'prefix' => 'dashboard'] ,function(){
    Route::namespace('Administrativo\GestionDocumental')->group(function () {
        //RUTAS CORRESPONDENCIA
        Route::get('correspondencia/create/{id}','CorrespondenciaController@create');
        Route::resource('correspondencia', 'CorrespondenciaController');

        //RUTAS BOLETINES    
        Route::Resource('boletines','BoletinesController');
        Route::get('/boletines/create','BoletinesController@create');

        //RUTAS ARCHIVO
        Route::get('/archivo/create','ArchivoController@create');
        Route::Resource('archivo','ArchivoController');
        Route::Resource('/archivo/manual','ManualContratController');
        Route::get('/archivo/manual/create','ManualContratController@create');
        Route::Resource('/archivo/plan','PlanAdquiController');
        Route::get('/archivo/plan/create','PlanAdquiController@create');

        Route::resource('/carpetas', 'CarpetaController');
        Route::get('/carpeta/{tipo}', 'CarpetaController@listar')->name('carpetas.listar');
        Route::post('/carpeta-archivo/{carpeta}/store', 'CarpetaController@storeArchivo')->name('carpeta.archivo.store');
        Route::get('/carpeta-archivo/{archivo}/delete', 'CarpetaController@deleteArchivo')->name('carpeta.archivo.delete');

    });

    Route::namespace('Administrativo\GestionDocumental\Acuerdos')->group(function () {
        //RUTAS ACUERDOS
        Route::Resource('acuerdos','AcuerdosController');
        Route::get('/acuerdos/create','AcuerdosController@create');
        Route::Resource('/acuerdos/proyectos','ProyectosAcuerdoController');
        Route::get('/acuerdos/proyectos/create','ProyectosAcuerdoController@create');
        Route::Resource('/acuerdos/actas','ActasController');
        Route::get('/acuerdos/actas/create','ActasController@create');
        Route::Resource('/acuerdos/resoluciones','ResolucionesController');
        Route::get('/acuerdos/resoluciones/create','ResolucionesController@create');

    });
});
