<?php
Route::group([ 'middleware' => 'auth', 'prefix' => 'coso'] ,function(){
    Route::get('/', "Coso\IndividuoController@index")->name('coso.individuo.index');
    Route::get('/individuo/{individuo}', "Coso\IndividuoController@show")->name('coso.individuo.show');
    Route::get('/exportar/{individuo}', "Coso\IndividuoController@pdf")->name('coso.individuo.pdf');
    Route::post('/', "Coso\IndividuoController@store")->name('coso.individuo.store');

    Route::get('/archivo/{individuo}', "Coso\ArchivoController@create")->name('coso.archivo.create');
    Route::post('/archivo/{individuo}', "Coso\ArchivoController@store")->name('coso.archivo.store');

    Route::get('/comidas/{individuo}', "Coso\ComidaController@create")->name('coso.comida.create');
    Route::post('/comidas/{individuo}', "Coso\ComidaController@store")->name('coso.comida.store');

    Route::get('/veterinario/{individuo}', "Coso\VeterinarioController@create")->name('coso.veterinario.create');
    Route::post('/veterinario/{individuo}', "Coso\VeterinarioController@store")->name('coso.veterinario.store');
});
