<?php
Route::group([ 'middleware' => 'auth', 'prefix' => 'sistemas'] ,function(){
    Route::get('/blanco', function(){
        return view('blanco');
    })->name('blanco');
});
