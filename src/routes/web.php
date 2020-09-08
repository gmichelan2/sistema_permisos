<?php

    use Illuminate\Support\Facades\Route;

    Route::get('/test', function(){
        return "hola";
    });

    Route::get('/home', 'HomeController@index')->name('home');

    Route::resource(config('SistemaPermisos.RouteRole'), 'Gmichelan2\Sistema_permisos\Http\Controllers\RoleController')->names('role')->middleware(['web']);//names porque son varios recursos

    Route::get('/user/{user}/password','Gmichelan2\Sistema_permisos\Http\Controllers\UserController@editContraseña')->name('passwordown.edit');
    Route::post('/user/{user}', 'Gmichelan2\Sistema_permisos\Http\Controllers\UserController@updateContraseña')->name('passwordown.update');


    Route::resource(config('SistemaPermisos.RouteUser'),'Gmichelan2\Sistema_permisos\Http\Controllers\UserController')->names('user')->middleware(['web']);//['except'=>['create','store']] se pueden restringir